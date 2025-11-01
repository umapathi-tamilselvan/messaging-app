<div class="d-flex mb-3 {{ isset($message['isSender']) && $message['isSender'] ? 'justify-content-end' : 'justify-content-start' }}">
    <div class="p-3 rounded-3 shadow-sm {{ isset($message['isSender']) && $message['isSender'] ? 'bg-primary text-white' : 'bg-white border' }}" 
         style="max-width: 70%; word-wrap: break-word;">
        <div class="mb-1">{{ $message['content'] ?? $message }}</div>
        <div class="small text-end mt-2 {{ isset($message['isSender']) && $message['isSender'] ? 'text-white-50' : 'text-muted' }}">
            {{ isset($message['time']) ? $message['time'] : now()->format('H:i') }}
        </div>
    </div>
</div>
