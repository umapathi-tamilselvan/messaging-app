@extends('chat.layout')

@section('chat-content')

<!-- Chat Header -->
<div class="d-flex align-items-center border-bottom bg-white px-3 py-2" style="min-height: 60px;">
    <div class="position-relative me-3">
        @if($user->profile_picture)
            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                 class="rounded-circle" 
                 width="40" 
                 height="40" 
                 alt="{{ $user->name }}"
                 style="object-fit: cover;">
        @else
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                 style="width: 40px; height: 40px; font-size: 16px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" 
              style="width: 10px; height: 10px;"></span>
    </div>
    <div class="flex-grow-1">
        <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
        <small class="text-success">Online</small>
    </div>
    <div class="ms-auto d-flex gap-1">
        <button class="btn btn-light btn-sm" type="button" title="Call">
            <i class="bi bi-telephone"></i>
        </button>
        <button class="btn btn-light btn-sm" type="button" title="Video Call">
            <i class="bi bi-camera-video"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" title="More options">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> View Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-search"></i> Search</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-mute"></i> Mute</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<span id="current-user-id" data-user-id="{{ $currentUser->id }}" style="display: none;"></span>
<span id="current-chat-user-id" data-user-id="{{ $user->id }}" style="display: none;"></span>

<!-- Messages Area -->
<div class="flex-grow-1 overflow-auto p-3" id="chatBody" style="background-color: #e5ddd5; background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cdefs%3E%3Cpattern id=\'grid\' width=\'100\' height=\'100\' patternUnits=\'userSpaceOnUse\'%3E%3Cpath d=\'M 100 0 L 0 0 0 100\' fill=\'none\' stroke=\'%23e5ddd5\' stroke-width=\'1\'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=\'100\' height=\'100\' fill=\'url(%23grid)\' /%3E%3C/svg%3E'); min-height: 0;">
    <!-- Empty state for messages -->
    <div id="empty-messages" class="d-flex flex-column justify-content-center align-items-center h-100 text-center text-muted">
        <i class="bi bi-lock-fill mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
        <p class="mb-0 small">Messages are end-to-end encrypted</p>
        <small>Start a conversation with {{ $user->name }}</small>
    </div>
    
    <!-- Messages will be dynamically added here -->
    <div id="messages-container" style="display: none;"></div>
</div>

<!-- Input Bar -->
@include('chat.components.input-bar', ['user' => $user])

@endsection