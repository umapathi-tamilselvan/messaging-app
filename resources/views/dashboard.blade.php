@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid h-100 p-0">
    <div class="row g-0 h-100">
        <!-- Sidebar - Conversations List (Instagram Style) -->
        <div class="col-md-4 col-lg-3 border-end bg-white" id="conversationsSidebar" style="border-right: var(--ig-border);">
            <div class="d-flex flex-column h-100">
                <!-- Header - Instagram Style -->
                <div class="p-3 border-bottom bg-white" style="border-bottom: var(--ig-border);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold" style="color: var(--ig-gray-600); font-size: 16px;">Messages</h5>
                        <button class="btn btn-sm rounded-circle" id="newConversationBtn" data-bs-toggle="modal" data-bs-target="#newConversationModal" title="New Conversation" style="background: transparent; color: var(--ig-gray-600); border: none; width: 32px; height: 32px; padding: 0;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div class="input-group" style="border-radius: var(--border-radius); overflow: hidden; background: var(--ig-gray-50); border: none;">
                        <span class="input-group-text bg-transparent border-0" style="background: transparent !important;">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0" id="searchConversations" placeholder="Search" style="background: transparent !important; font-size: 14px; padding: 8px 0;">
                    </div>
                </div>

                <!-- Conversations List -->
                <div class="flex-grow-1 overflow-auto" id="conversationsList">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-8 col-lg-9 d-flex flex-column" id="chatArea">
            <!-- Empty State - Instagram Style -->
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4" id="emptyChatState">
                <div class="empty-chat-icon mb-4">
                    <i class="bi bi-chat-left-text" style="font-size: 5rem; color: var(--ig-gray-400);"></i>
                </div>
                <h4 class="fw-semibold mb-2" style="color: var(--ig-gray-600); font-size: 20px;">Select a conversation</h4>
                <p class="text-muted mb-4" style="color: var(--ig-gray-500); font-size: 14px;">Choose a conversation from the sidebar to start messaging</p>
                <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#newConversationModal" style="border-radius: var(--border-radius); font-size: 14px; font-weight: 600;">
                    <i class="bi bi-plus-circle me-2"></i>Start New Conversation
                </button>
            </div>

            <!-- Active Chat (Hidden by default) - Instagram Style -->
            <div class="d-none flex-column h-100" id="activeChat">
                <!-- Chat Header - Instagram Style -->
                <div class="p-3 border-bottom bg-white" style="border-bottom: var(--ig-border);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-primary rounded-circle me-3" id="chatAvatar" style="width: 40px; height: 40px;">
                                <span class="d-flex align-items-center justify-content-center h-100 text-white fw-bold" id="chatAvatarText" style="font-size: 16px;"></span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold" id="chatTitle" style="color: var(--ig-gray-600); font-size: 14px; font-weight: 600;"></h6>
                                <small class="text-muted" id="chatSubtitle" style="color: var(--ig-gray-500); font-size: 12px;"></small>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm rounded-circle" id="chatInfoBtn" title="Chat Info" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: transparent; border: none; color: var(--ig-gray-600);">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Container - Instagram Style -->
                <div class="flex-grow-1 overflow-auto p-4 bg-white" id="messagesContainer" style="background: var(--white);">
                    <div id="messagesList"></div>
                </div>

                <!-- Message Input - Instagram Style -->
                <div class="p-3 border-top bg-white" style="border-top: var(--ig-border);">
                    <form id="messageForm">
                        <div class="input-group" style="background: var(--ig-gray-50); border-radius: var(--border-radius-xl); padding: 4px;">
                            <button type="button" class="btn border-0" id="attachBtn" title="Attach file" style="background: transparent; color: var(--ig-gray-600); padding: 8px 12px;">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <input type="text" class="form-control border-0" id="messageInput" placeholder="Message..." autocomplete="off" style="background: transparent; font-size: 14px; padding: 8px 0;">
                            <button type="button" class="btn border-0" id="emojiBtn" title="Add emoji" style="background: transparent; color: var(--ig-gray-600); padding: 8px 12px;">
                                <i class="bi bi-emoji-smile"></i>
                            </button>
                            <button type="submit" class="btn border-0" id="sendBtn" style="padding: 0; margin: 0 4px;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                    <input type="file" id="fileInput" class="d-none" multiple>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal - Instagram Style -->
<div class="modal fade" id="newConversationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--border-radius-lg); border: none; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="border-bottom: var(--ig-border); padding: 16px 20px;">
                <h5 class="modal-title fw-bold" style="color: var(--ig-gray-600); font-size: 16px;">New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: none; opacity: 0.5;"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#privateTab" type="button">
                            <i class="bi bi-person me-1"></i>Private
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#groupTab" type="button">
                            <i class="bi bi-people me-1"></i>Group
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="privateTab">
                        <div class="mb-3">
                            <label class="form-label">Select User</label>
                            <input type="text" class="form-control" id="userSearch" placeholder="Search by phone number...">
                            <div id="usersList" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="groupTab">
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="groupName" placeholder="Enter group name...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Add Members</label>
                            <input type="text" class="form-control" id="groupUserSearch" placeholder="Search by phone number...">
                            <div id="groupUsersList" class="mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createConversationBtn">Create</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
@endpush

@push('scripts')
<!-- Pusher for real-time messaging -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // API_BASE_URL is not needed - axios.defaults.baseURL is already set to '/api' in app.js
    const AUTH_TOKEN = localStorage.getItem('auth_token');
    
    // Set up Axios default headers
    if (AUTH_TOKEN) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${AUTH_TOKEN}`;
    }
    
    // Pusher configuration (fallback to polling if Pusher not configured)
    window.PUSHER_CONFIG = {
        enabled: {{ env('BROADCAST_DRIVER') === 'pusher' && env('PUSHER_APP_KEY') ? 'true' : 'false' }},
        key: '{{ env('PUSHER_APP_KEY', '') }}',
        cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
        encrypted: true,
        authEndpoint: '{{ url('/broadcasting/auth') }}',
        auth: {
            headers: {
                'Authorization': `Bearer ${AUTH_TOKEN}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }
    };
</script>
<script src="{{ asset('js/messaging.js') }}"></script>
@endpush

