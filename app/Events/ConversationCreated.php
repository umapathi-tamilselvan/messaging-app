<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Conversation $conversation)
    {
        $this->conversation->load(['users', 'latestMessage']);
    }

    public function broadcastOn(): array
    {
        // Broadcast to a user-specific channel for each participant
        // We need to reload users since they might not be loaded yet
        if (!$this->conversation->relationLoaded('users')) {
            $this->conversation->load('users');
        }
        
        $channels = [];
        foreach ($this->conversation->users as $user) {
            $channels[] = new PrivateChannel("user.{$user->id}");
        }
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'conversation:created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->conversation->id,
            'type' => $this->conversation->type,
            'name' => $this->conversation->name,
            'created_by' => $this->conversation->created_by,
            'users' => $this->conversation->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                ];
            }),
            'created_at' => $this->conversation->created_at->toIso8601String(),
        ];
    }
}

