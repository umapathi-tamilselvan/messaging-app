<div class="border-top bg-white p-3 d-flex align-items-center gap-2">
    <button class="btn btn-light btn-sm" type="button" title="Emoji">
        <i class="bi bi-emoji-smile"></i>
    </button>
    <input type="text" 
           class="form-control rounded-pill border-0 bg-light" 
           placeholder="Type a message..." 
           id="message-input"
           autocomplete="off"
           style="font-size: 15px;">
    <button class="btn btn-light btn-sm" type="button" title="Attach">
        <i class="bi bi-paperclip"></i>
    </button>
    <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" 
            type="submit" 
            id="send-button"
            style="width: 40px; height: 40px;"
            title="Send">
        <i class="bi bi-send-fill"></i>
    </button>
</div>

@push('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
@endpush
