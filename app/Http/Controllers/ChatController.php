<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        
        // Get all users except the current user as potential chats
        $users = User::where('id', '!=', $currentUser->id)->get();
        $user = null; // No user selected initially
        
        return view('chat.empty', compact('users', 'user', 'currentUser'));
    }

    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Ensure the user is not trying to chat with themselves
        if ($user->id === $currentUser->id) {
            return redirect()->route('chats.index');
        }
        
        // Get all users except the current user as potential chats
        $users = User::where('id', '!=', $currentUser->id)->get();
        
        return view('chat.chatbox', compact('users', 'user', 'currentUser'));
    }
}
