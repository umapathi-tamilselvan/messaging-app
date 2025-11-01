@extends('layouts.app')

@section('title', $user ? 'Chat with ' . $user->name . ' - Messaging App' : 'Chats - Messaging App')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endpush

@section('content')
<div class="container-fluid px-0 h-100" style="max-width: 100%; height: 100vh;">
    <div class="row g-0 chat-container mx-auto h-100" style="max-width: 1400px;">
        <!-- Left Sidebar: Chat List (Slack Style) -->
        <div class="col-md-4 col-lg-3 chat-sidebar">
            <!-- Workspace Header -->
            <div class="sidebar-workspace-header">
                <div class="d-flex align-items-center">
                    <div class="fw-bold" style="font-size: 18px;">Messaging App</div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="dropdown" style="opacity: 0.8;">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="sidebar-search">
                <input type="text" placeholder="Search" id="chat-search">
            </div>
            
            <!-- Direct Messages Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-header" onclick="toggleSection('dm-section')">
                    <div class="d-flex align-items-center">
                        <span class="sidebar-section-toggle collapsed" id="dm-section-toggle">▾</span>
                        <span style="margin-left: 8px;">Direct Messages</span>
                    </div>
                </div>
                <div class="chat-list" id="dm-section" style="display: none;">
                    @if($users->count() > 0)
                        @foreach($users as $chatUser)
                            <a href="{{ route('chats.show', $chatUser) }}" 
                               class="chat-list-item {{ $user && $user->id === $chatUser->id ? 'active' : '' }}">
                                <div class="position-relative">
                                    @if($chatUser->profile_picture)
                                        <img src="{{ asset('storage/' . $chatUser->profile_picture) }}" 
                                             alt="{{ $chatUser->name }}" 
                                             class="user-avatar">
                                    @else
                                        <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center" style="font-size: 14px;">
                                            {{ strtoupper(substr($chatUser->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="user-status-indicator"></span>
                                </div>
                                <div class="chat-list-item-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="chat-list-item-name">{{ $chatUser->name }}</span>
                                        <span class="message-timestamp">12:00</span>
                                    </div>
                                    <div class="last-message-preview">{{ $chatUser->status }}</div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="chat-list-item" style="color: #ab9ba9; font-style: italic; justify-content: center; padding: 16px;">
                            No conversations yet
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- User Profile Footer -->
            <div class="sidebar-user-profile" data-bs-toggle="dropdown">
                <div class="sidebar-user-info">
                    @if($currentUser->profile_picture)
                        <img src="{{ asset('storage/' . $currentUser->profile_picture) }}" 
                             alt="{{ $currentUser->name }}" 
                             class="sidebar-avatar">
                    @else
                        <div class="sidebar-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate" style="font-size: 15px;">{{ $currentUser->name }}</div>
                        <small style="color: #ab9ba9; font-size: 13px;">Active</small>
                    </div>
                    <span class="sidebar-user-status"></span>
                </div>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> Profile</a>
                    <a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Preferences</a>
                    <hr class="dropdown-divider">
                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Panel: Chat Area -->
        <div class="col-md-8 col-lg-9 chat-area">
            @if($user)
                <!-- Chat Header -->
                <div class="chat-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                     alt="{{ $user->name }}" 
                                     class="user-avatar">
                            @else
                                <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person"></i>
                                </div>
                            @endif
                            <span class="user-status-indicator"></span>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">Online</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-link text-muted p-2"><i class="bi bi-telephone"></i></button>
                        <button class="btn btn-link text-muted p-2"><i class="bi bi-camera-video"></i></button>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-2" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> View Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-mute"></i> Mute</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-search"></i> Search</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="chat-messages" id="messages-container">
                    <div class="empty-chat-state">
                        <i class="bi bi-lock-fill mb-2" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="mb-0">Messages are end-to-end encrypted</p>
                        <small>Start a conversation with {{ $user->name }}</small>
                    </div>
                    
                    <!-- Example messages (will be replaced with actual messages) -->
                    <!--
                    <div class="message-bubble message-received">
                        <div>Hey! How are you?</div>
                        <div class="message-time">10:30 AM</div>
                    </div>
                    <div class="message-bubble message-sent">
                        <div>I'm doing great, thanks for asking!</div>
                        <div class="message-time">10:31 AM</div>
                    </div>
                    -->
                </div>
                
                <!-- Chat Input Area -->
                <div class="chat-input-area">
                    <form id="chat-form" class="d-flex align-items-center gap-2">
                        @csrf
                        <button type="button" class="btn btn-link text-muted p-2"><i class="bi bi-emoji-smile fs-5"></i></button>
                        <input type="text" 
                               class="form-control chat-input flex-grow-1" 
                               id="message-input"
                               placeholder="Type a message" 
                               autocomplete="off">
                        <button type="button" class="btn btn-link text-muted p-2"><i class="bi bi-paperclip fs-5"></i></button>
                        <button type="submit" class="send-button">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            @else
                <!-- Empty State: No Chat Selected -->
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots" style="font-size: 5rem; opacity: 0.3;"></i>
                        <h4 class="mt-3 mb-2">Select a chat to start messaging</h4>
                        <p>Choose a conversation from the left panel to begin</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
@endpush
@endsection