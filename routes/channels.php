<?php

use App\Broadcasting\ConversationChannel;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversation}', ConversationChannel::class);

// User-specific channel for receiving conversation updates
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

