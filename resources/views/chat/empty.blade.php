@extends('chat.layout')

@section('chat-content')
<div class="d-flex flex-column justify-content-center align-items-center h-100 text-center text-muted">
    <i class="bi bi-chat-square-text display-4 mb-3" style="opacity: 0.5;"></i>
    <h5 class="fw-normal mb-2">Select a chat to start messaging</h5>
    <p class="mb-0 small mb-4">Choose a conversation from the left panel to begin.</p>
    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#newChatModal">
        <i class="bi bi-plus-circle me-2"></i>New Chat
    </button>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newChatModalLabel">Select a user to start chatting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="userSearchInput" placeholder="Search users...">
                </div>
                <div class="list-group">
                    @forelse($allUsers as $chatUser)
                        <a href="{{ route('chats.show', $chatUser) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="position-relative me-3">
                                @if($chatUser->profile_picture)
                                    <img src="{{ asset('storage/' . $chatUser->profile_picture) }}" 
                                         class="rounded-circle" 
                                         width="45" 
                                         height="45" 
                                         alt="{{ $chatUser->name }}"
                                         style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 45px; height: 45px; font-size: 18px;">
                                        {{ strtoupper(substr($chatUser->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" 
                                      style="width: 12px; height: 12px;"></span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="mb-0 fw-semibold text-truncate">{{ $chatUser->name }}</h6>
                                <small class="text-muted text-truncate d-block">{{ Str::limit($chatUser->status, 30) }}</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    @empty
                        <div class="text-center p-4 text-muted">
                            <p class="mb-0">No users available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearchInput');
    const userItems = document.querySelectorAll('#newChatModal .list-group-item');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            userItems.forEach(item => {
                const userName = item.querySelector('h6')?.textContent.toLowerCase() || '';
                if (userName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
@endsection
