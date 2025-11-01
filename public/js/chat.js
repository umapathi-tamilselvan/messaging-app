// Chat search functionality for sidebar
document.addEventListener('DOMContentLoaded', function() {
    const chatSearch = document.getElementById('chat-search');
    
    if (chatSearch) {
        chatSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const chatItems = document.querySelectorAll('.list-group-item, .chat-list-item');
            
            chatItems.forEach(item => {
                const nameEl = item.querySelector('h6, .chat-list-item-name');
                if (nameEl) {
                    const name = nameEl.textContent.toLowerCase();
                    if (name.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    }
});

// Message input functionality
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const chatBody = document.getElementById('chatBody');
    const emptyMessages = document.getElementById('empty-messages');
    const messagesContainer = document.getElementById('messages-container');
    const chatForm = document.getElementById('chat-form');
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function sendMessage() {
        const message = input.value.trim();
        if (!message) return;
        
        // Hide empty state
        if (emptyMessages) emptyMessages.style.display = 'none';
        if (messagesContainer) messagesContainer.style.display = 'block';
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = 'd-flex mb-3 justify-content-end';
        
        const now = new Date();
        const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
        
        messageDiv.innerHTML = `
            <div class="p-3 rounded-3 shadow-sm bg-primary text-white" style="max-width: 70%; word-wrap: break-word;">
                <div class="mb-1">${escapeHtml(message)}</div>
                <div class="small text-end mt-2 text-white-50">${time}</div>
            </div>
        `;
        
        if (messagesContainer) {
            messagesContainer.appendChild(messageDiv);
        } else if (chatBody) {
            if (emptyMessages) emptyMessages.style.display = 'none';
            chatBody.appendChild(messageDiv);
        }
        
        // Scroll to bottom
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
        
        // Clear input
        input.value = '';
        
        // In a real implementation, send via AJAX
        console.log('Message sent:', message);
    }
    
    // Handle form submission
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = input.value.trim();
            
            if (message) {
                const messagesContainerEl = document.getElementById('messages-container');
                
                // Remove empty state
                const emptyState = messagesContainerEl?.querySelector('.empty-chat-state');
                if (emptyState) {
                    emptyState.remove();
                }
                
                // Create message bubble
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message-bubble message-sent';
                
                const now = new Date();
                const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                
                messageDiv.innerHTML = `
                    <div>${escapeHtml(message)}</div>
                    <div class="message-time">${time}</div>
                `;
                
                if (messagesContainerEl) {
                    messagesContainerEl.appendChild(messageDiv);
                    scrollToBottom();
                }
                
                input.value = '';
                
                // In a real implementation, send via AJAX to backend
                console.log('Message sent:', message);
            }
        });
    }
    
    // Send button click
    if (sendButton) {
        sendButton.addEventListener('click', sendMessage);
    }
    
    // Enter key to send (Shift+Enter for new line)
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (chatForm) {
                    chatForm.dispatchEvent(new Event('submit'));
                } else {
                    sendMessage();
                }
            }
        });
    }
});

// Auto-scroll to bottom of messages
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Toggle section collapse/expand
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    const toggle = document.getElementById(sectionId + '-toggle');
    
    if (section && toggle) {
        if (section.style.display === 'none') {
            section.style.display = 'block';
            toggle.classList.remove('collapsed');
        } else {
            section.style.display = 'none';
            toggle.classList.add('collapsed');
        }
    }
}

// Initialize - expand DM section by default
document.addEventListener('DOMContentLoaded', function() {
    const dmSection = document.getElementById('dm-section');
    const dmToggle = document.getElementById('dm-section-toggle');
    if (dmSection && dmToggle) {
        dmSection.style.display = 'block';
        dmToggle.classList.remove('collapsed');
    }
    scrollToBottom();
});

