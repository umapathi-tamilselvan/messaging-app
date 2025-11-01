<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function index(Request $request, Conversation $conversation)
    {
        // Verify user is part of conversation
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $beforeId = $request->get('before_id');
        $limit = $request->get('limit', 50);

        $query = $conversation->messages()
            ->with(['user', 'attachment', 'replyTo.user'])
            ->orderBy('created_at', 'desc');

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->limit($limit)->get()->reverse()->values();

        return response()->json([
            'data' => $messages,
            'has_more' => $messages->count() === $limit,
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        // Verify user is part of conversation
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required_without:attachment_id|string|max:5000',
            'type' => 'required|in:text,image,voice,file,video',
            'reply_to_id' => 'sometimes|exists:messages,id',
            'attachment_id' => 'sometimes|exists:attachments,id',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'type' => $request->type,
            'reply_to_id' => $request->reply_to_id,
        ]);

        // Create delivery statuses for all participants
        $participants = $conversation->users->pluck('id');
        foreach ($participants as $userId) {
            MessageStatus::create([
                'message_id' => $message->id,
                'user_id' => $userId,
                'status' => $userId === $request->user()->id ? 'delivered' : 'sent',
            ]);
        }

        // Update unread count for other users
        $conversation->users()
            ->where('user_id', '!=', $request->user()->id)
            ->increment('unread_count');

        // Load relationships
        $message->load(['user', 'attachment', 'replyTo.user']);

        // Broadcast via WebSocket
        Broadcast::channel("conversation.{$conversation->id}", function ($user) use ($conversation) {
            return $conversation->users->contains($user->id);
        });

        event(new \App\Events\MessageSent($message));

        // Clear cache
        Cache::forget("user:{$request->user()->id}:conversations:*");
        foreach ($participants as $userId) {
            Cache::forget("user:{$userId}:conversations:*");
        }

        return response()->json($message, 201);
    }

    public function markAsSeen(Request $request, Message $message)
    {
        // Verify user is part of conversation
        if (!$message->conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $status = MessageStatus::where('message_id', $message->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($status && $status->status !== 'seen') {
            $status->update([
                'status' => 'seen',
                'seen_at' => now(),
            ]);

            // Broadcast read receipt
            event(new \App\Events\MessageSeen($message, $request->user()));
        }

        return response()->json([
            'seen_at' => $status->seen_at,
        ]);
    }

    public function destroy(Request $request, Message $message)
    {
        // Verify user owns the message or is admin
        if ($message->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->delete();

        // Broadcast deletion
        event(new \App\Events\MessageDeleted($message));

        return response()->json(['deleted' => true, 'deleted_at' => $message->deleted_at], 200);
    }
}
