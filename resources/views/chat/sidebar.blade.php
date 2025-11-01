<div class="p-3 border-bottom">
    <input type="text" 
           class="form-control form-control-sm rounded-pill" 
           placeholder="Search chats..." 
           id="chat-search">
</div>

<div class="list-group list-group-flush overflow-auto" style="height: calc(100vh - 140px); max-height: calc(100vh - 140px);">
    @if($users->count() > 0)
        @foreach($users as $chatUser)
            <a href="{{ route('chats.show', $chatUser) }}" 
               class="list-group-item list-group-item-action d-flex align-items-center border-0 border-bottom px-3 py-3 hover-bg-light {{ (isset($user) && $user && $user->id === $chatUser->id) ? 'bg-light' : '' }}"
               style="transition: background-color 0.15s;">
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
                <small class="text-muted ms-2" style="font-size: 0.75rem;">{{ now()->format('H:i') }}</small>
            </a>
        @endforeach
    @else
        <div class="text-center p-5 text-muted">
            <i class="bi bi-chat-dots display-6 mb-3"></i>
            <p class="mb-0">No chats available</p>
            <small>Start chatting with other users!</small>
        </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
@endpush