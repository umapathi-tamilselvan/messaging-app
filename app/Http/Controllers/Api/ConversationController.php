<?php

namespace App\Http\Controllers\Api;

use App\Events\ConversationCreated;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $cacheKey = "user:{$user->id}:conversations:page:{$page}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user, $limit) {
            return $user->conversations()
                ->with(['latestMessage.user', 'users'])
                ->withCount('messages')
                ->orderByPivot('updated_at', 'desc')
                ->paginate($limit);
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:private,group',
            'user_id' => 'required_if:type,private|exists:users,id',
            'name' => 'required_if:type,group|string|max:255',
            'user_ids' => 'required_if:type,group|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $user = $request->user();

        if ($request->type === 'private') {
            // Check if conversation already exists between these two users
            $existingConversation = Conversation::where('type', 'private')
                ->whereHas('users', function ($query) use ($user, $request) {
                    $query->where('user_id', $user->id)
                          ->orWhere('user_id', $request->user_id);
                })
                ->withCount('users')
                ->having('users_count', '=', 2)
                ->first();
            
            if ($existingConversation && 
                $existingConversation->users->contains($user->id) && 
                $existingConversation->users->contains($request->user_id)) {
                return response()->json($existingConversation->load(['users', 'latestMessage']), 200);
            }

            $conversation = Conversation::create([
                'type' => 'private',
                'created_by' => $user->id,
            ]);

            $conversation->users()->attach([$user->id, $request->user_id]);
        } else {
            $conversation = Conversation::create([
                'type' => 'group',
                'name' => $request->name,
                'created_by' => $user->id,
            ]);

            $userIds = array_unique(array_merge([$user->id], $request->user_ids));
            $conversation->users()->attach($userIds);
        }

        // Load relationships before broadcasting
        $conversation->load(['users', 'latestMessage']);

        // Clear cache for all participants
        $participantIds = $conversation->users->pluck('id');
        foreach ($participantIds as $participantId) {
            // Clear cache for multiple pages (conversations are paginated)
            for ($page = 1; $page <= 10; $page++) {
                Cache::forget("user:{$participantId}:conversations:page:{$page}");
            }
            // Also try to clear with wildcard pattern (for Redis)
            try {
                Cache::forget("user:{$participantId}:conversations:*");
            } catch (\Exception $e) {
                // Wildcard might not be supported by all cache drivers
            }
        }

        // Broadcast conversation created event to all participants
        event(new ConversationCreated($conversation));

        return response()->json($conversation, 201);
    }

    public function show(Request $request, Conversation $conversation)
    {
        // Verify user is part of conversation
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($conversation->load(['users', 'latestMessage']));
    }

    public function update(Request $request, Conversation $conversation)
    {
        // Verify user is part of conversation
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar_url' => 'sometimes|url',
        ]);

        $conversation->update($request->only(['name', 'avatar_url']));

        // Clear cache
        Cache::forget("user:{$request->user()->id}:conversations:*");

        return response()->json($conversation->load(['users', 'latestMessage']));
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        // Verify user is part of conversation
        if (!$conversation->users->contains($request->user()->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->delete();

        // Clear cache
        Cache::forget("user:{$request->user()->id}:conversations:*");

        return response()->json(['message' => 'Conversation deleted'], 200);
    }
}
