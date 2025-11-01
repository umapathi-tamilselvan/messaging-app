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

// Global chat state
window.ChatState = {
    currentUserId: null,
    currentChatUserId: null,
    lastMessageId: 0,
    pollInterval: null,
    isActiveTab: true,
    globalNotificationInterval: null,
    lastGlobalCheckId: 0,
};

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Show browser notification
function showNotification(title, body, icon = null, tag = null) {
    if ('Notification' in window && Notification.permission === 'granted') {
        // Always show notification (the calling code handles when to show)
        new Notification(title, {
            body: body,
            icon: icon || '/favicon.ico',
            badge: '/favicon.ico',
            tag: tag || 'message-notification',
            requireInteraction: false,
        });
    }
}

// Check if tab is active
document.addEventListener('visibilitychange', function() {
    window.ChatState.isActiveTab = !document.hidden;
    if (window.ChatState.isActiveTab && window.ChatState.currentChatUserId) {
        checkForNewMessages();
    }
    // Check for global notifications immediately when tab becomes active
    if (window.ChatState.isActiveTab) {
        checkForGlobalNotifications();
    }
});

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Format time
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

// Scroll to bottom of messages
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    const chatBody = document.getElementById('chatBody');
    
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
    if (chatBody) {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
}

// Render a message
function renderMessage(message, currentUserId) {
    const isSent = message.sender_id == currentUserId;
    const messageDiv = document.createElement('div');
    messageDiv.className = `d-flex mb-3 ${isSent ? 'justify-content-end' : 'justify-content-start'}`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    const time = formatTime(message.created_at);
    
    messageDiv.innerHTML = `
        <div class="p-3 rounded-3 shadow-sm ${isSent ? 'bg-primary text-white' : 'bg-white'}" style="max-width: 70%; word-wrap: break-word;">
            <div class="mb-1">${escapeHtml(message.message)}</div>
            <div class="small text-end mt-2 ${isSent ? 'text-white-50' : 'text-muted'}">${time}</div>
        </div>
    `;
    
    return messageDiv;
}

// Load messages for a chat
async function loadMessages(userId) {
    try {
        const response = await fetch(`/api/messages/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Failed to load messages');
        }

        const data = await response.json();
        const messagesContainer = document.getElementById('messages-container');
        const emptyMessages = document.getElementById('empty-messages');
        
        if (!messagesContainer) return;

        messagesContainer.innerHTML = '';
        
        if (data.messages && data.messages.length > 0) {
            if (emptyMessages) emptyMessages.style.display = 'none';
            messagesContainer.style.display = 'block';
            
            data.messages.forEach(message => {
                const messageEl = renderMessage(message, window.ChatState.currentUserId);
                messagesContainer.appendChild(messageEl);
                
                if (message.id > window.ChatState.lastMessageId) {
                    window.ChatState.lastMessageId = message.id;
                }
            });
            
            scrollToBottom();
        } else {
            if (emptyMessages) emptyMessages.style.display = 'flex';
            messagesContainer.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Send a message
async function sendMessage() {
    const input = document.getElementById('message-input');
    const message = input?.value.trim();
    
    if (!message || !window.ChatState.currentChatUserId) return;

    const messageText = message;
    input.value = '';
    
    // Optimistically add message to UI
    const messagesContainer = document.getElementById('messages-container');
    const emptyMessages = document.getElementById('empty-messages');
    
    if (messagesContainer) {
        if (emptyMessages) emptyMessages.style.display = 'none';
        messagesContainer.style.display = 'block';
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                       || document.querySelector('input[name="_token"]')?.value;
        
        const response = await fetch('/api/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                receiver_id: window.ChatState.currentChatUserId,
                message: messageText,
            }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Failed to send message');
        }

        const data = await response.json();
        
        // Reload messages to get the properly formatted message from server
        await loadMessages(window.ChatState.currentChatUserId);
        
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
        input.value = messageText; // Restore message on error
    }
}

// Check for new messages
async function checkForNewMessages() {
    if (!window.ChatState.currentChatUserId || window.ChatState.lastMessageId === 0) {
        return;
    }

    try {
        const response = await fetch(
            `/api/messages/${window.ChatState.currentChatUserId}/check?last_message_id=${window.ChatState.lastMessageId}`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            }
        );

        if (!response.ok) return;

        const data = await response.json();
        const messagesContainer = document.getElementById('messages-container');
        
        if (data.messages && data.messages.length > 0 && messagesContainer) {
            const wasAtBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop <= messagesContainer.clientHeight + 100;
            
            data.messages.forEach(message => {
                // Check if message already exists
                if (!messagesContainer.querySelector(`[data-message-id="${message.id}"]`)) {
                    const messageEl = renderMessage(message, window.ChatState.currentUserId);
                    messagesContainer.appendChild(messageEl);
                    
                    // Show notification for new messages if not from current user
                    // Only show if tab is not active OR if not viewing this specific chat
                    if (message.sender_id != window.ChatState.currentUserId) {
                        const isViewingThisChat = window.ChatState.currentChatUserId == message.sender_id;
                        if (!window.ChatState.isActiveTab || !isViewingThisChat) {
                            showNotification(
                                `New message from ${message.sender?.name || 'Someone'}`,
                                message.message.substring(0, 80) + (message.message.length > 80 ? '...' : ''),
                                null,
                                `chat-notification-${message.sender_id}`
                            );
                        }
                    }
                }
                
                if (message.id > window.ChatState.lastMessageId) {
                    window.ChatState.lastMessageId = message.id;
                }
            });
            
            if (wasAtBottom) {
                scrollToBottom();
            }
        }
    } catch (error) {
        console.error('Error checking for new messages:', error);
    }
}

// Start polling for new messages
function startPolling() {
    if (window.ChatState.pollInterval) {
        clearInterval(window.ChatState.pollInterval);
    }
    
    // Check immediately
    checkForNewMessages();
    
    // Then check every 3 seconds
    window.ChatState.pollInterval = setInterval(checkForNewMessages, 3000);
}

// Stop polling
function stopPolling() {
    if (window.ChatState.pollInterval) {
        clearInterval(window.ChatState.pollInterval);
        window.ChatState.pollInterval = null;
    }
}

// Message input functionality and global notifications
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission immediately
    requestNotificationPermission();
    
    const input = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const chatForm = document.getElementById('chat-form');
    
    // Get current user ID from page
    const currentUserIdEl = document.getElementById('current-user-id');
    const currentChatUserIdEl = document.getElementById('current-chat-user-id');
    
    if (currentUserIdEl) {
        window.ChatState.currentUserId = parseInt(currentUserIdEl.dataset.userId);
    }
    
    if (currentChatUserIdEl) {
        window.ChatState.currentChatUserId = parseInt(currentChatUserIdEl.dataset.userId);
        
        // Load messages when chat is opened
        loadMessages(window.ChatState.currentChatUserId).then(() => {
            startPolling();
        });
    }
    
    // Start global notification polling if we have a user ID (works on all authenticated pages)
    if (window.ChatState.currentUserId) {
        startGlobalNotificationPolling();
    }
    
    // Handle form submission
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    
    // Send button click
    if (sendButton) {
        sendButton.addEventListener('click', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    
    // Enter key to send (Shift+Enter for new line)
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
});

// Auto-scroll to bottom of messages
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    const chatBody = document.getElementById('chatBody');
    
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
    if (chatBody) {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
}

// Check for global notifications (all conversations)
async function checkForGlobalNotifications() {
    if (!window.ChatState.currentUserId) {
        return;
    }

    try {
        const response = await fetch(
            `/api/messages/unread/new?last_message_id=${window.ChatState.lastGlobalCheckId}`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            }
        );

        if (!response.ok) return;

        const data = await response.json();
        
        if (data.messages_by_sender && data.messages_by_sender.length > 0) {
            // Show notification for each sender with new messages
            data.messages_by_sender.forEach(senderData => {
                const sender = senderData.sender;
                const messages = senderData.messages;
                
                if (messages && messages.length > 0) {
                    const latestMessage = messages[0]; // Most recent message
                    const messageCount = messages.length;
                    const messageText = messageCount === 1 
                        ? latestMessage.message.substring(0, 80) + (latestMessage.message.length > 80 ? '...' : '')
                        : `${messageCount} new messages`;
                    
                    const senderName = sender?.name || 'Someone';
                    
                    // Only show notification if:
                    // 1. Tab is not active, OR
                    // 2. User is not viewing this specific chat
                    const isViewingThisChat = window.ChatState.currentChatUserId == sender.id;
                    if (!window.ChatState.isActiveTab || !isViewingThisChat) {
                        showNotification(
                            `New message${messageCount > 1 ? 's' : ''} from ${senderName}`,
                            messageText,
                            null,
                            `notification-${sender.id}`
                        );
                    }
                }
            });

            // Update last check ID
            if (data.last_message_id > window.ChatState.lastGlobalCheckId) {
                window.ChatState.lastGlobalCheckId = data.last_message_id;
            }
        }

        // Update unread count in UI if element exists
        if (data.unread_count !== undefined) {
            updateUnreadCountBadge(data.unread_count);
        }
    } catch (error) {
        console.error('Error checking for global notifications:', error);
    }
}

// Update unread count badge in UI
function updateUnreadCountBadge(count) {
    // Try to find and update unread count badge in navbar
    let badge = document.getElementById('unread-count-badge');
    
    if (count > 0) {
        if (!badge) {
            // Create badge if it doesn't exist
            const chatsLink = document.querySelector('a[href*="chats"]');
            if (chatsLink) {
                badge = document.createElement('span');
                badge.id = 'unread-count-badge';
                badge.className = 'badge bg-danger rounded-pill ms-1';
                badge.textContent = count > 99 ? '99+' : count;
                chatsLink.appendChild(badge);
            }
        } else {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-block';
        }
    } else if (badge) {
        badge.style.display = 'none';
    }
}

// Start global notification polling
function startGlobalNotificationPolling() {
    if (window.ChatState.globalNotificationInterval) {
        clearInterval(window.ChatState.globalNotificationInterval);
    }
    
    // Check immediately
    checkForGlobalNotifications();
    
    // Then check every 5 seconds for new messages
    window.ChatState.globalNotificationInterval = setInterval(checkForGlobalNotifications, 5000);
}

// Stop global notification polling
function stopGlobalNotificationPolling() {
    if (window.ChatState.globalNotificationInterval) {
        clearInterval(window.ChatState.globalNotificationInterval);
        window.ChatState.globalNotificationInterval = null;
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

// Cleanup on page unload (single handler)
window.addEventListener('beforeunload', function() {
    stopGlobalNotificationPolling();
    stopPolling();
});
