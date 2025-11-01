<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get messages between the current user and another user.
     */
    public function index(User $user)
    {
        $currentUser = Auth::user();
        
        if ($user->id === $currentUser->id) {
            return response()->json(['error' => 'Cannot chat with yourself'], 400);
        }

        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $currentUser->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
               ->where('receiver_id', $currentUser->id)
               ->where('read', false)
               ->update(['read' => true]);

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $currentUser = Auth::user();
        $receiverId = $request->input('receiver_id');

        if ($receiverId == $currentUser->id) {
            return response()->json(['error' => 'Cannot send message to yourself'], 400);
        }

        $message = Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $receiverId,
            'message' => $request->input('message'),
            'read' => false,
        ]);

        $message->load(['sender', 'receiver']);

        return response()->json([
            'message' => $message,
            'success' => true
        ], 201);
    }

    /**
     * Get unread message count for the current user.
     */
    public function unreadCount()
    {
        $currentUser = Auth::user();
        
        $unreadCount = Message::where('receiver_id', $currentUser->id)
                             ->where('read', false)
                             ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get recent messages/conversations for the current user.
     */
    public function conversations()
    {
        $currentUser = Auth::user();
        
        // Get the latest message from each conversation
        $conversations = Message::where('sender_id', $currentUser->id)
            ->orWhere('receiver_id', $currentUser->id)
            ->selectRaw('
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as other_user_id,
                MAX(created_at) as last_message_time,
                MAX(id) as last_message_id
            ', [$currentUser->id])
            ->groupBy('other_user_id')
            ->orderBy('last_message_time', 'desc')
            ->get();

        $conversationsData = [];
        foreach ($conversations as $conv) {
            $otherUser = User::find($conv->other_user_id);
            if ($otherUser) {
                $lastMessage = Message::find($conv->last_message_id);
                $unreadCount = Message::where('sender_id', $otherUser->id)
                                     ->where('receiver_id', $currentUser->id)
                                     ->where('read', false)
                                     ->count();

                $conversationsData[] = [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            }
        }

        return response()->json([
            'conversations' => $conversationsData
        ]);
    }

    /**
     * Check for new messages since last message ID.
     */
    public function checkNew(User $user, Request $request)
    {
        $currentUser = Auth::user();
        $lastMessageId = $request->input('last_message_id', 0);

        $newMessages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $currentUser->id);
        })
        ->where('id', '>', $lastMessageId)
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark new messages as read if they're from the other user
        if ($newMessages->isNotEmpty()) {
            Message::where('sender_id', $user->id)
                   ->where('receiver_id', $currentUser->id)
                   ->where('id', '>', $lastMessageId)
                   ->where('read', false)
                   ->update(['read' => true]);
        }

        return response()->json([
            'messages' => $newMessages
        ]);
    }

    /**
     * Get new unread messages from all conversations.
     */
    public function newUnreadMessages(Request $request)
    {
        $currentUser = Auth::user();
        $lastCheckId = $request->input('last_message_id', 0);

        // Get all new unread messages received by the current user
        $newMessages = Message::where('receiver_id', $currentUser->id)
            ->where('read', false)
            ->where('id', '>', $lastCheckId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the highest message ID to track for next check
        $lastMessageId = $newMessages->isNotEmpty() 
            ? $newMessages->max('id') 
            : $lastCheckId;

        // Group messages by sender for better notification handling
        $messagesBySender = [];
        foreach ($newMessages as $message) {
            $senderId = $message->sender_id;
            if (!isset($messagesBySender[$senderId])) {
                $messagesBySender[$senderId] = [
                    'sender' => $message->sender,
                    'messages' => [],
                ];
            }
            $messagesBySender[$senderId]['messages'][] = $message;
        }

        return response()->json([
            'new_messages' => $newMessages,
            'messages_by_sender' => array_values($messagesBySender),
            'last_message_id' => $lastMessageId,
            'unread_count' => Message::where('receiver_id', $currentUser->id)
                                    ->where('read', false)
                                    ->count(),
        ]);
    }
}
