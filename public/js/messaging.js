/* ===================================
   Messaging Interface JavaScript
   =================================== */

(function() {
    'use strict';
    
    // API_BASE_URL is not needed since axios.defaults.baseURL is already set to '/api' in app.js
    // We'll use relative URLs that axios will automatically prepend with baseURL
    let currentConversationId = null;
    let conversations = [];
    let messages = [];
    let messagePage = 1;
    let isLoadingMessages = false;
    
    // DOM Elements - Support both home and chats screen
    const conversationsList = document.getElementById('conversationsList') || document.getElementById('conversationsListChats');
    const messagesContainer = document.getElementById('messagesContainer') || document.getElementById('messagesContainerChats');
    const messagesList = document.getElementById('messagesList') || document.getElementById('messagesListChats');
    const messageForm = document.getElementById('messageForm') || document.getElementById('messageFormChats');
    const messageInput = document.getElementById('messageInput') || document.getElementById('messageInputChats');
    const sendBtn = document.getElementById('sendBtn') || document.getElementById('sendBtnChats');
    const emptyChatState = document.getElementById('emptyChatState') || document.getElementById('emptyChatStateChats');
    const activeChat = document.getElementById('activeChat') || document.getElementById('activeChatChats');
    const newConversationBtn = document.getElementById('newConversationBtn') || document.getElementById('newConversationBtnChats');
    const fileInput = document.getElementById('fileInput') || document.getElementById('fileInputChats');
    const attachBtn = document.getElementById('attachBtn') || document.getElementById('attachBtnChats');
    
    // Check authentication
    const authToken = localStorage.getItem('auth_token');
    if (!authToken) {
        window.location.href = '/login';
        return;
    }
    
    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
    
    // Load conversations on page load
    loadConversations();
    
    // Event Listeners
    messageForm.addEventListener('submit', sendMessage);
    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileSelect);
    
    // Auto-resize textarea (if using textarea instead of input)
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Load Conversations
    async function loadConversations() {
        try {
            const response = await axios.get(`/conversations`);
            // Handle paginated response (Laravel paginate returns data in 'data' property)
            let conversationsData = response.data;
            if (conversationsData && conversationsData.data) {
                conversations = conversationsData.data; // Paginated response
            } else if (Array.isArray(conversationsData)) {
                conversations = conversationsData; // Direct array
            } else {
                conversations = [];
            }
            renderConversations();
        } catch (error) {
            console.error('Error loading conversations:', error);
            window.utils.showNotification('Failed to load conversations', 'error');
            conversationsList.innerHTML = `
                <div class="text-center py-5">
                    <p class="text-muted">Failed to load conversations</p>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">Retry</button>
                </div>
            `;
        }
    }
    
    // Render Conversations
    function renderConversations() {
        if (conversations.length === 0) {
            conversationsList.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No conversations yet</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                        Start Conversation
                    </button>
                </div>
            `;
            return;
        }
        
        conversationsList.innerHTML = conversations.map(conv => {
            const otherUser = conv.users?.find(u => u.id !== getCurrentUserId()) || conv.users?.[0];
            const name = conv.name || otherUser?.name || otherUser?.phone || 'Unknown';
            const initials = window.utils.getInitials(name);
            const color = window.utils.generateColor(name);
            const lastMessage = conv.latest_message?.message || 'No messages yet';
            const unreadCount = conv.pivot?.unread_count || 0;
            
            return `
                <div class="conversation-item ${conv.id === currentConversationId ? 'active' : ''}" 
                     onclick="selectConversation(${conv.id})">
                    <div class="d-flex align-items-center">
                        <div class="conversation-avatar" style="background-color: ${color}">
                            ${initials}
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">${escapeHtml(name)}</div>
                            <div class="conversation-preview">${escapeHtml(lastMessage)}</div>
                        </div>
                        <div class="conversation-meta">
                            <div class="conversation-time">${formatTime(conv.updated_at)}</div>
                            ${unreadCount > 0 ? `<div class="conversation-badge">${unreadCount}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // Select Conversation
    window.selectConversation = async function(conversationId) {
        currentConversationId = conversationId;
        messagePage = 1;
        messages = [];
        
        // Update UI
        renderConversations();
        
        // Show active chat and hide empty state
        if (emptyChatState) emptyChatState.classList.add('d-none');
        if (activeChat) {
            activeChat.classList.remove('d-none');
            // On mobile, hide sidebar when opening chat
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('chatsSidebar');
                if (sidebar) sidebar.classList.remove('show');
            }
        }
        
        // Update chat header - Support both screens
        const conversation = conversations.find(c => c.id === conversationId);
        if (conversation) {
            const otherUser = conversation.users?.find(u => u.id !== getCurrentUserId()) || conversation.users?.[0];
            const name = conversation.name || otherUser?.name || otherUser?.phone || 'Unknown';
            const initials = window.utils.getInitials(name);
            const color = window.utils.generateColor(name);
            
            // Update for home screen
            const chatTitle = document.getElementById('chatTitle');
            const chatSubtitle = document.getElementById('chatSubtitle');
            const chatAvatarText = document.getElementById('chatAvatarText');
            const chatAvatar = document.getElementById('chatAvatar');
            
            // Update for chats screen
            const chatTitleChats = document.getElementById('chatTitleChats');
            const chatSubtitleChats = document.getElementById('chatSubtitleChats');
            const chatAvatarTextChats = document.getElementById('chatAvatarTextChats');
            const chatAvatarChats = document.getElementById('chatAvatarChats');
            
            if (chatTitle) {
                chatTitle.textContent = name;
                chatSubtitle.textContent = 'Online';
                chatAvatarText.textContent = initials;
                chatAvatar.style.backgroundColor = color;
            }
            
            if (chatTitleChats) {
                chatTitleChats.textContent = name;
                chatSubtitleChats.textContent = 'Online';
                chatAvatarTextChats.textContent = initials;
                chatAvatarChats.style.backgroundColor = color;
            }
        }
        
        // Subscribe to real-time updates (will be defined later)
        if (typeof subscribeToConversation === 'function') {
            subscribeToConversation(conversationId);
        }
        
        // Load messages
        await loadMessages(conversationId);
    };
    
    // Load Messages
    async function loadMessages(conversationId, beforeId = null) {
        if (isLoadingMessages) return;
        
        isLoadingMessages = true;
        messagesList.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
        
        try {
            const params = { limit: 50 };
            if (beforeId) params.before_id = beforeId;
            
            const response = await axios.get(`/conversations/${conversationId}/messages`, { params });
            const newMessages = response.data.data || response.data || [];
            
            if (beforeId) {
                messages = [...newMessages, ...messages];
            } else {
                messages = newMessages.reverse();
            }
            
            renderMessages();
            scrollToBottom();
        } catch (error) {
            console.error('Error loading messages:', error);
            window.utils.showNotification('Failed to load messages', 'error');
            messagesList.innerHTML = '<div class="text-center py-5 text-muted">Failed to load messages</div>';
        } finally {
            isLoadingMessages = false;
        }
    }
    
    // Render Messages
    function renderMessages() {
        if (messages.length === 0) {
            messagesList.innerHTML = '<div class="text-center py-5 text-muted">No messages yet. Start the conversation!</div>';
            return;
        }
        
        const currentUserId = getCurrentUserId();
        
        messagesList.innerHTML = messages.map(msg => {
            const isSent = msg.user_id === currentUserId;
            const time = window.utils.formatTime(msg.created_at);
            const hasAttachment = msg.attachment || (msg.type && msg.type !== 'text');
            
            let attachmentHtml = '';
            if (hasAttachment) {
                if (msg.type === 'image' && msg.attachment?.path) {
                    const imageUrl = msg.attachment.full_url || `/storage/${msg.attachment.path}`;
                    attachmentHtml = `<img src="${imageUrl}" alt="Image" class="img-fluid rounded mb-2" style="max-width: 300px; cursor: pointer;" onclick="window.open('${imageUrl}', '_blank')">`;
                } else if (msg.type === 'video' && msg.attachment?.path) {
                    const videoUrl = msg.attachment.full_url || `/storage/${msg.attachment.path}`;
                    attachmentHtml = `<video controls class="mb-2" style="max-width: 300px;"><source src="${videoUrl}" type="${msg.attachment.mime_type}"></video>`;
                } else {
                    const iconClass = msg.type === 'voice' ? 'bi-mic-fill' : 'bi-file-earmark';
                    const fileUrl = msg.attachment?.full_url || (msg.attachment?.path ? `/storage/${msg.attachment.path}` : '#');
                    attachmentHtml = `<div class="attachment-preview mb-2">
                        <i class="bi ${iconClass} fs-4"></i>
                        <a href="${fileUrl}" target="_blank" class="text-decoration-none">${escapeHtml(msg.message || msg.attachment?.path || 'Attachment')}</a>
                    </div>`;
                }
            }
            
            const messageText = msg.message && msg.type === 'text' ? `<p class="message-text mb-0">${escapeHtml(msg.message)}</p>` : '';
            
            return `
                <div class="message ${isSent ? 'sent' : 'received'}">
                    <div class="message-bubble">
                        ${attachmentHtml}
                        ${messageText}
                        <div class="message-meta">
                            <span>${time}</span>
                            ${isSent ? '<span class="message-status"><i class="bi bi-check2-all"></i></span>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // Send Message
    async function sendMessage(e) {
        e.preventDefault();
        
        if (!currentConversationId) {
            window.utils.showNotification('Please select a conversation', 'warning');
            return;
        }
        
        const message = messageInput.value.trim();
        if (!message) return;
        
        // Disable input
        messageInput.disabled = true;
        sendBtn.disabled = true;
        
        try {
            const response = await axios.post(`/conversations/${currentConversationId}/messages`, {
                message: message,
                type: 'text'
            });
            
            // Add message to list (only if not already present)
            const newMessage = response.data.data || response.data;
            if (!messages.find(m => m.id === newMessage.id)) {
                messages.push(newMessage);
                renderMessages();
                scrollToBottom();
            }
            
            // Clear input
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
            // Reload conversations to update latest message
            loadConversations();
        } catch (error) {
            console.error('Error sending message:', error);
            window.utils.showNotification('Failed to send message', 'error');
        } finally {
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();
        }
    }
    
    // Handle File Select
    async function handleFileSelect(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;
        
        if (!currentConversationId) {
            window.utils.showNotification('Please select a conversation first', 'warning');
            return;
        }
        
        // Process each file
        for (const file of files) {
            await uploadFile(file);
        }
        
        // Reset file input
        fileInput.value = '';
    }
    
    // Upload File to S3
    async function uploadFile(file) {
        try {
            // Determine file type
            const mimeType = file.type;
            let messageType = 'file';
            if (mimeType.startsWith('image/')) {
                messageType = 'image';
            } else if (mimeType.startsWith('video/')) {
                messageType = 'video';
            } else if (mimeType.startsWith('audio/')) {
                messageType = 'voice';
            }
            
            // Get presigned URL
            const signResponse = await axios.post('/upload/sign', {
                filename: file.name,
                mime_type: mimeType,
                size: file.size
            });
            
            const { upload_url, attachment_id } = signResponse.data;
            
            // Upload to S3
            await axios.put(upload_url, file, {
                headers: {
                    'Content-Type': mimeType
                }
            });
            
            // Send message with attachment
            const response = await axios.post(`/conversations/${currentConversationId}/messages`, {
                message: file.name,
                type: messageType,
                attachment_id: attachment_id
            });
            
            // Add message to list (only if not already present)
            const newMessage = response.data.data || response.data;
            if (!messages.find(m => m.id === newMessage.id)) {
                messages.push(newMessage);
                renderMessages();
                scrollToBottom();
            }
            
            // Reload conversations
            loadConversations();
            
            window.utils.showNotification('File uploaded successfully', 'success');
        } catch (error) {
            console.error('Error uploading file:', error);
            window.utils.showNotification('Failed to upload file: ' + (error.response?.data?.message || error.message), 'error');
        }
    }
    
    // Utility Functions
    function getCurrentUserId() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        return user.id;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatTime(dateString) {
        return window.utils.formatTime(dateString);
    }
    
    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
    
    // Auto-scroll to bottom on new messages
    const observer = new MutationObserver(() => {
        scrollToBottom();
    });
    
    if (messagesList) {
        observer.observe(messagesList, { childList: true });
    }
    
    // Real-time WebSocket (Pusher) Integration
    let pusher = null;
    let currentChannel = null;
    let userChannel = null;
    
    function initializePusher() {
        if (!window.PUSHER_CONFIG || !window.PUSHER_CONFIG.enabled || !window.Pusher) {
            // Fallback to polling if Pusher not available
            console.log('Pusher not configured, using polling');
            startPolling();
            return;
        }
        
        try {
            pusher = new Pusher(window.PUSHER_CONFIG.key, {
                cluster: window.PUSHER_CONFIG.cluster,
                encrypted: window.PUSHER_CONFIG.encrypted,
                authEndpoint: window.PUSHER_CONFIG.authEndpoint,
                auth: window.PUSHER_CONFIG.auth
            });
            
            console.log('Pusher initialized');
            
            // Subscribe to user channel for conversation updates
            subscribeToUserChannel();
            
            // Subscribe to conversation when one is selected
            subscribeToConversation(currentConversationId);
        } catch (error) {
            console.error('Error initializing Pusher:', error);
            startPolling();
        }
    }
    
    function subscribeToUserChannel() {
        if (!pusher) return;
        
        const currentUserId = getCurrentUserId();
        if (!currentUserId) return;
        
        // Subscribe to user-specific channel
        const channelName = `private-user.${currentUserId}`;
        userChannel = pusher.subscribe(channelName);
        
        // Listen for new conversation created
        userChannel.bind('conversation:created', async (data) => {
            console.log('New conversation created:', data);
            // Reload conversations list to show new conversation
            await loadConversations();
            
            // Show notification
            const conversationName = data.name || data.users?.find(u => u.id !== getCurrentUserId())?.name || 'New conversation';
            window.utils.showNotification(`New conversation: ${conversationName}`, 'info');
        });
        
        console.log(`Subscribed to user channel: ${channelName}`);
    }
    
    function subscribeToConversation(conversationId) {
        if (!pusher || !conversationId) return;
        
        // Unsubscribe from previous channel
        if (currentChannel) {
            pusher.unsubscribe(currentChannel);
        }
        
        // Subscribe to new conversation channel
        currentChannel = `private-conversation.${conversationId}`;
        const channel = pusher.subscribe(currentChannel);
        
        // Listen for new messages
        channel.bind('message:new', (data) => {
            const newMessage = data;
            // Convert to same format as API response if needed
            const messageToAdd = {
                id: newMessage.id,
                conversation_id: newMessage.conversation_id,
                user_id: newMessage.user?.id || newMessage.user_id,
                message: newMessage.message,
                type: newMessage.type,
                created_at: newMessage.created_at,
                attachment: newMessage.attachment,
                reply_to_id: newMessage.reply_to_id
            };
            
            // Only add if not already in messages (check by ID)
            if (!messages.find(m => m.id === messageToAdd.id)) {
                messages.push(messageToAdd);
                renderMessages();
                scrollToBottom();
                
                // Update conversation list
                loadConversations();
            }
        });
        
        // Listen for message seen events
        channel.bind('message:seen', (data) => {
            // Update message status if needed
            const message = messages.find(m => m.id === data.message_id);
            if (message) {
                renderMessages();
            }
        });
        
        // Listen for deleted messages
        channel.bind('message:deleted', (data) => {
            messages = messages.filter(m => m.id !== data.message_id);
            renderMessages();
        });
        
        console.log(`Subscribed to conversation ${conversationId}`);
    }
    
    // Fallback polling function
    function startPolling() {
        // Poll for new messages in current conversation
        setInterval(() => {
            if (currentConversationId && messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                loadNewMessages(currentConversationId, lastMessage.id);
            }
        }, 5000); // Poll every 5 seconds
        
        // Poll for new conversations (refresh conversation list)
        setInterval(() => {
            loadConversations();
        }, 10000); // Poll every 10 seconds for new conversations
    }
    
    // Initialize Pusher on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePusher);
    } else {
        initializePusher();
    }
    
    // Load only new messages
    async function loadNewMessages(conversationId, afterId) {
        try {
            const response = await axios.get(`/conversations/${conversationId}/messages`, {
                params: { after_id: afterId, limit: 50 }
            });
            
            const newMessages = response.data.data || response.data || [];
            if (newMessages.length > 0) {
                // Only add messages that don't already exist (check by ID)
                const existingIds = messages.map(m => m.id);
                const uniqueNewMessages = newMessages.filter(m => !existingIds.includes(m.id));
                if (uniqueNewMessages.length > 0) {
                    messages = [...messages, ...uniqueNewMessages];
                    renderMessages();
                }
            }
        } catch (error) {
            // Silently fail for polling
            console.error('Error polling messages:', error);
        }
    }
    
    // User Search - Debounced
    let searchTimeout;
    const userSearchInput = document.getElementById('userSearch');
    const usersList = document.getElementById('usersList');
    const groupUserSearchInput = document.getElementById('groupUserSearch');
    const groupUsersList = document.getElementById('groupUsersList');
    let selectedUsers = []; // For group creation
    
    if (userSearchInput) {
        userSearchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 1) {
                usersList.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(async () => {
                await searchUsers(query, usersList, false);
            }, 300);
        });
    }
    
    if (groupUserSearchInput) {
        groupUserSearchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 1) {
                groupUsersList.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(async () => {
                await searchUsers(query, groupUsersList, true);
            }, 300);
        });
    }
    
    // Search Users
    async function searchUsers(query, container, isGroup) {
        try {
            const response = await axios.get('/users/search', { params: { q: query } });
            const users = response.data.data || response.data || [];
            
            if (users.length === 0) {
                container.innerHTML = '<div class="text-muted p-2">No users found</div>';
                return;
            }
            
            container.innerHTML = users.map(user => {
                const isSelected = isGroup && selectedUsers.some(u => u.id === user.id);
                const initials = window.utils.getInitials(user.name || user.phone);
                const color = window.utils.generateColor(user.name || user.phone);
                
                if (isGroup) {
                    return `
                        <div class="d-flex align-items-center p-2 border rounded mb-2 ${isSelected ? 'bg-light' : ''}" style="cursor: pointer;" onclick="toggleGroupUser(${user.id}, '${escapeHtml(user.name || user.phone)}')">
                            <div class="conversation-avatar" style="width: 40px; height: 40px; font-size: 0.875rem; background-color: ${color};">${initials}</div>
                            <div class="ms-2 flex-grow-1">
                                <div class="fw-semibold">${escapeHtml(user.name || 'Unknown')}</div>
                                <small class="text-muted">${escapeHtml(user.phone)}</small>
                            </div>
                            ${isSelected ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-plus-circle text-muted"></i>'}
                        </div>
                    `;
                } else {
                    return `
                        <div class="d-flex align-items-center p-2 border rounded mb-2" style="cursor: pointer;" onclick="selectUserForPrivateChat(${user.id}, '${escapeHtml(user.name || user.phone)}')">
                            <div class="conversation-avatar" style="width: 40px; height: 40px; font-size: 0.875rem; background-color: ${color};">${initials}</div>
                            <div class="ms-2 flex-grow-1">
                                <div class="fw-semibold">${escapeHtml(user.name || 'Unknown')}</div>
                                <small class="text-muted">${escapeHtml(user.phone)}</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    `;
                }
            }).join('');
        } catch (error) {
            console.error('Error searching users:', error);
            container.innerHTML = '<div class="text-danger p-2">Failed to search users</div>';
        }
    }
    
    // Select User for Private Chat
    window.selectUserForPrivateChat = async function(userId, userName) {
        try {
            const response = await axios.post('/conversations', {
                type: 'private',
                user_id: userId
            });
            
            const conversation = response.data.data || response.data;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('newConversationModal'));
            if (modal) modal.hide();
            
            // Clear search
            if (userSearchInput) userSearchInput.value = '';
            if (usersList) usersList.innerHTML = '';
            
            // Reload conversations list first to ensure it appears
            await loadConversations();
            
            // Select the conversation
            await selectConversation(conversation.id);
            
            window.utils.showNotification(`Started conversation with ${userName}`, 'success');
        } catch (error) {
            console.error('Error creating conversation:', error);
            window.utils.showNotification('Failed to create conversation', 'error');
        }
    };
    
    // Toggle Group User Selection
    window.toggleGroupUser = function(userId, userName) {
        const index = selectedUsers.findIndex(u => u.id === userId);
        if (index > -1) {
            selectedUsers.splice(index, 1);
        } else {
            selectedUsers.push({ id: userId, name: userName });
        }
        
        // Refresh search results
        const query = groupUserSearchInput?.value.trim() || '';
        if (query) {
            searchUsers(query, groupUsersList, true);
        }
    };
    
    // New Conversation Modal Handler
    const createConversationBtn = document.getElementById('createConversationBtn');
    if (createConversationBtn) {
        createConversationBtn.addEventListener('click', async function() {
            const activeTab = document.querySelector('.nav-link.active');
            const isGroup = activeTab?.getAttribute('data-bs-target') === '#groupTab';
            
            try {
                if (isGroup) {
                    const groupName = document.getElementById('groupName')?.value.trim();
                    if (!groupName) {
                        window.utils.showNotification('Please enter a group name', 'warning');
                        return;
                    }
                    
                    if (selectedUsers.length === 0) {
                        window.utils.showNotification('Please add at least one member', 'warning');
                        return;
                    }
                    
                    const userIds = selectedUsers.map(u => u.id);
                    const response = await axios.post('/conversations', {
                        type: 'group',
                        name: groupName,
                        user_ids: userIds
                    });
                    
                    const conversation = response.data.data || response.data;
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newConversationModal'));
                    if (modal) modal.hide();
                    
                    // Clear form
                    document.getElementById('groupName').value = '';
                    groupUserSearchInput.value = '';
                    groupUsersList.innerHTML = '';
                    selectedUsers = [];
                    
                    // Reload conversations list first to ensure it appears
                    await loadConversations();
                    
                    // Select the conversation
                    await selectConversation(conversation.id);
                    
                    window.utils.showNotification('Group created successfully', 'success');
                } else {
                    const selectedUserId = document.getElementById('userSearch')?.dataset.selectedUserId;
                    if (!selectedUserId) {
                        window.utils.showNotification('Please select a user', 'warning');
                        return;
                    }
                }
            } catch (error) {
                console.error('Error creating conversation:', error);
                window.utils.showNotification('Failed to create conversation: ' + (error.response?.data?.message || error.message), 'error');
            }
        });
    }
    
    // Reset modal on close
    const newConversationModal = document.getElementById('newConversationModal');
    if (newConversationModal) {
        newConversationModal.addEventListener('hidden.bs.modal', function() {
            // Reset all inputs
            if (userSearchInput) {
                userSearchInput.value = '';
                userSearchInput.dataset.selectedUserId = '';
            }
            if (usersList) usersList.innerHTML = '';
            if (groupUserSearchInput) groupUserSearchInput.value = '';
            if (groupUsersList) groupUsersList.innerHTML = '';
            selectedUsers = [];
            const groupNameInput = document.getElementById('groupName');
            if (groupNameInput) groupNameInput.value = '';
        });
    }
    
})();

