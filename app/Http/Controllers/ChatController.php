<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        
        // Get all users except the current user as potential chats (for new chat modal)
        $allUsers = User::where('id', '!=', $currentUser->id)->get();
        
        // Get users that have conversations (messages) with the current user
        $conversationUserIds = Message::where('sender_id', $currentUser->id)
            ->orWhere('receiver_id', $currentUser->id)
            ->selectRaw('CASE 
                WHEN sender_id = ? THEN receiver_id 
                ELSE sender_id 
            END as user_id', [$currentUser->id])
            ->distinct()
            ->pluck('user_id')
            ->toArray();
        
        // Also include users from session (for new conversations that haven't sent messages yet)
        $sessionUsers = session('conversation_users', []);
        $conversationUserIds = array_unique(array_merge($conversationUserIds, $sessionUsers));
        
        $users = User::whereIn('id', $conversationUserIds)
                    ->where('id', '!=', $currentUser->id)
                    ->get();
        
        $user = null; // No user selected initially
        
        return view('chat.empty', compact('users', 'allUsers', 'user', 'currentUser'));
    }

    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure the user is not trying to chat with themselves
        if ($user->id === $currentUser->id) {
            return redirect()->route('chats.index');
        }
        
        // Track that a conversation has been started with this user
        $conversationUsers = session('conversation_users', []);
        if (!in_array($user->id, $conversationUsers)) {
            $conversationUsers[] = $user->id;
            session(['conversation_users' => $conversationUsers]);
        }
        
        // Get all users except the current user as potential chats (for new chat modal)
        $allUsers = User::where('id', '!=', $currentUser->id)->get();
        
        // Get users that have conversations (messages) with the current user
        $conversationUserIds = Message::where('sender_id', $currentUser->id)
            ->orWhere('receiver_id', $currentUser->id)
            ->selectRaw('CASE 
                WHEN sender_id = ? THEN receiver_id 
                ELSE sender_id 
            END as user_id', [$currentUser->id])
            ->distinct()
            ->pluck('user_id')
            ->toArray();
        
        // Also include users from session
        $conversationUserIds = array_unique(array_merge($conversationUserIds, $conversationUsers));
        
        $users = User::whereIn('id', $conversationUserIds)
                    ->where('id', '!=', $currentUser->id)
                    ->get();
        
        return view('chat.chatbox', compact('users', 'allUsers', 'user', 'currentUser'));
    }
}
