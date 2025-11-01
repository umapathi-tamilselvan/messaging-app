<?php

namespace App\Broadcasting;

use App\Models\Conversation;
use App\Models\User;

class ConversationChannel
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Conversation $conversation): bool
    {
        return $conversation->users->contains($user->id);
    }
}
