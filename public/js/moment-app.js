/**
 * Moment App - Navigation and Screen Management
 */

(function() {
    'use strict';

    // Initialize app
    document.addEventListener('DOMContentLoaded', function() {
        initializeNavigation();
        initializeInteractions();
        initializeMessagesList();
    });

    /**
     * Initialize Bottom Navigation
     */
    function initializeNavigation() {
        const navItems = document.querySelectorAll('.bottom-nav-item');
        const screens = document.querySelectorAll('.screen');

        navItems.forEach(item => {
            item.addEventListener('click', function() {
                const targetScreen = this.getAttribute('data-screen');
                
                // Update active nav item
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');

                // Update icon states - Reset all icons first
                navItems.forEach(nav => {
                    const navIcon = nav.querySelector('i');
                    if (navIcon) {
                        const iconClass = navIcon.className;
                        // Reset filled icons to outline
                        if (iconClass.includes('house-fill')) navIcon.className = 'bi bi-house';
                        if (iconClass.includes('chat-fill')) navIcon.className = 'bi bi-chat';
                        if (iconClass.includes('person-fill')) navIcon.className = 'bi bi-person';
                        if (iconClass.includes('heart-fill')) navIcon.className = 'bi bi-heart';
                    }
                });

                // Set active icon to filled
                const icon = this.querySelector('i');
                if (icon) {
                    const iconClass = icon.className;
                    if (iconClass.includes('house') && !iconClass.includes('fill')) {
                        icon.className = 'bi bi-house-fill';
                    } else if (iconClass.includes('chat') && !iconClass.includes('fill')) {
                        icon.className = 'bi bi-chat-fill';
                    } else if (iconClass.includes('person') && !iconClass.includes('fill')) {
                        icon.className = 'bi bi-person-fill';
                    } else if (iconClass.includes('heart') && !iconClass.includes('fill')) {
                        icon.className = 'bi bi-heart-fill';
                    }
                }

                // Update active screen
                screens.forEach(screen => screen.classList.remove('active'));
                const targetScreenElement = document.getElementById(targetScreen + 'Screen');
                if (targetScreenElement) {
                    targetScreenElement.classList.add('active');
                    
                    // On mobile, hide sidebar when viewing main area
                    if (window.innerWidth <= 768 && targetScreen === 'chats') {
                        const sidebar = document.getElementById('chatsSidebar');
                        if (sidebar) {
                            sidebar.classList.remove('show');
                        }
                    }
                }
            });
        });
        
        // Mobile sidebar toggle
        const backButton = document.querySelector('.chats-chat-header .icon-button');
        if (backButton) {
            backButton.addEventListener('click', function() {
                const sidebar = document.getElementById('chatsSidebar');
                if (sidebar && window.innerWidth <= 768) {
                    sidebar.classList.add('show');
                }
            });
        }
    }

    /**
     * Initialize Interactive Elements
     */
    function initializeInteractions() {
        // Post Like Button
        const likeButtons = document.querySelectorAll('.post-like-btn');
        likeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('bi-heart')) {
                    icon.className = 'bi bi-heart-fill';
                    this.classList.add('liked');
                } else {
                    icon.className = 'bi bi-heart';
                    this.classList.remove('liked');
                }
            });
        });

        // Post Save Button
        const saveButtons = document.querySelectorAll('.post-save-btn');
        saveButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('bi-bookmark')) {
                    icon.className = 'bi bi-bookmark-fill';
                } else {
                    icon.className = 'bi bi-bookmark';
                }
            });
        });

        // Camera Mode Toggle
        const cameraModeButtons = document.querySelectorAll('.camera-mode-btn');
        const cameraCaptureBtn = document.getElementById('cameraCaptureBtn');
        
        cameraModeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                cameraModeButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const mode = this.getAttribute('data-mode');
                const circle = cameraCaptureBtn.querySelector('.camera-capture-circle');
                
                if (mode === 'video') {
                    if (circle) {
                        circle.style.borderRadius = '8px';
                        circle.style.width = '30px';
                        circle.style.height = '30px';
                    }
                } else {
                    if (circle) {
                        circle.style.borderRadius = '50%';
                        circle.style.width = '60px';
                        circle.style.height = '60px';
                    }
                }
            });
        });

        // Activity Tabs
        const activityTabs = document.querySelectorAll('.activity-tab');
        activityTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                activityTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const tabType = this.getAttribute('data-tab');
                // Load activities based on tab type
                // TODO: Implement activity loading based on tab
            });
        });
    }

    /**
     * Initialize Instagram-Style Messages List (Home Screen)
     */
    function initializeMessagesList() {
        const messagesListScreen = document.querySelector('.messages-list-screen');
        if (!messagesListScreen) return;

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                themeToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
            }

            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                themeToggle.innerHTML = isDark 
                    ? '<i class="bi bi-sun-fill"></i>' 
                    : '<i class="bi bi-moon-fill"></i>';
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }

        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const chatList = document.getElementById('chatList');
        
        if (searchInput && chatList) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                const allChatItems = Array.from(chatList.querySelectorAll('.chat-item'));
                
                allChatItems.forEach(item => {
                    const name = item.querySelector('.chat-name')?.textContent.toLowerCase() || '';
                    const message = item.querySelector('.chat-message')?.textContent.toLowerCase() || '';
                    
                    if (searchTerm === '' || name.includes(searchTerm) || message.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Load and render conversations in Instagram style
        loadInstagramConversations();

        // Update username in header
        const currentUser = JSON.parse(localStorage.getItem('user') || '{}');
        const usernameEl = document.getElementById('currentUsername');
        if (usernameEl && currentUser.name) {
            usernameEl.textContent = currentUser.name;
        }
    }

    /**
     * Load conversations and render in Instagram style
     */
    async function loadInstagramConversations() {
        const chatList = document.getElementById('chatList');
        const loadingChats = document.getElementById('loadingChats');
        
        if (!chatList) return;

        try {
            const authToken = localStorage.getItem('auth_token');
            if (!authToken) return;

            axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
            
            const response = await axios.get('/conversations');
            const conversations = response.data.data || [];

            if (loadingChats) {
                loadingChats.style.display = 'none';
            }

            if (conversations.length === 0) {
                chatList.innerHTML = `
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

            // Calculate total unread count for badge
            const totalUnread = conversations.reduce((sum, conv) => {
                return sum + (conv.pivot?.unread_count || 0);
            }, 0);

            // Update messages badge
            const messagesBadge = document.getElementById('messagesBadge');
            if (messagesBadge) {
                if (totalUnread > 0) {
                    messagesBadge.textContent = totalUnread > 9 ? '9+' : totalUnread;
                    messagesBadge.style.display = 'flex';
                } else {
                    messagesBadge.style.display = 'none';
                }
            }

            chatList.innerHTML = conversations.map(conv => {
                const otherUser = conv.users?.find(u => u.id !== getCurrentUserId()) || conv.users?.[0];
                const name = conv.name || otherUser?.name || otherUser?.phone || 'Unknown';
                const initials = getInitials(name);
                const color = generateColor(name);
                const lastMessage = conv.latest_message?.message || 'No messages yet';
                const unreadCount = conv.pivot?.unread_count || 0;
                const hasStory = Math.random() > 0.7; // Random for demo
                const isOnline = Math.random() > 0.5; // Random for demo
                const isPinned = conv.pivot?.pinned || false;
                
                // Format time
                let timeText = '';
                if (conv.latest_message?.created_at) {
                    const messageDate = new Date(conv.latest_message.created_at);
                    const now = new Date();
                    const diffMs = now - messageDate;
                    const diffMins = Math.floor(diffMs / 60000);
                    const diffHours = Math.floor(diffMs / 3600000);
                    const diffDays = Math.floor(diffMs / 86400000);
                    
                    if (diffMins < 60) {
                        timeText = diffMins < 1 ? 'now' : `${diffMins}m`;
                    } else if (diffHours < 24) {
                        timeText = `${diffHours}h`;
                    } else if (diffDays < 7) {
                        timeText = `${diffDays}d`;
                    } else {
                        timeText = `${Math.floor(diffDays / 7)}w`;
                    }
                }

                // Determine message preview
                let messagePreview = lastMessage;
                let statusIcon = '';
                let mediaIcon = '';
                
                if (lastMessage.includes('Photo') || lastMessage.includes('📷')) {
                    mediaIcon = '<span class="media-icon">📷</span>';
                    messagePreview = 'Photo';
                } else if (lastMessage.includes('Voice') || lastMessage.includes('🎤')) {
                    mediaIcon = '<span class="media-icon">🎤</span>';
                    messagePreview = 'Voice message';
                } else if (lastMessage.includes('Document') || lastMessage.includes('📎')) {
                    mediaIcon = '<span class="media-icon">📎</span>';
                    messagePreview = lastMessage.split('.pdf')[0] + '.pdf';
                } else {
                    // Check if message was sent by current user
                    const isSentByMe = conv.latest_message?.user_id === getCurrentUserId();
                    if (isSentByMe) {
                        statusIcon = '<span class="status-icon read">✓✓</span>';
                    } else {
                        statusIcon = '<span class="status-icon">✓</span>';
                    }
                }

                return `
                    <div class="chat-item ${unreadCount > 0 ? 'unread' : ''}" onclick="openInstagramChat(${conv.id}, '${name.replace(/'/g, "\\'")}')">
                        <div class="chat-avatar ${hasStory ? 'has-story' : ''}">
                            <div class="chat-avatar-inner" style="background: ${color};">
                                ${hasStory ? `<div class="avatar-content" style="background: ${color};">${initials}</div>` : initials}
                            </div>
                            ${isOnline ? '<span class="chat-online-dot"></span>' : ''}
                        </div>
                        <div class="chat-content">
                            <div class="chat-header">
                                <span class="chat-name">${name}</span>
                                <span class="chat-time">${timeText}</span>
                            </div>
                            <div class="chat-preview">
                                ${statusIcon}
                                ${mediaIcon}
                                <span class="chat-message">${messagePreview}</span>
                                ${unreadCount > 0 ? `<span class="chat-badge">${unreadCount > 9 ? '9+' : unreadCount}</span>` : ''}
                            </div>
                        </div>
                        ${isPinned ? '<span class="pin-icon">📌</span>' : ''}
                    </div>
                `;
            }).join('');

        } catch (error) {
            console.error('Error loading conversations:', error);
            if (loadingChats) {
                loadingChats.innerHTML = `
                    <div class="text-center py-5">
                        <p class="text-danger">Error loading conversations</p>
                        <button class="btn btn-primary btn-sm" onclick="location.reload()">Retry</button>
                    </div>
                `;
            }
        }
    }

    /**
     * Get current user ID
     */
    function getCurrentUserId() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        return user.id || null;
    }

    /**
     * Get initials from name
     */
    function getInitials(name) {
        if (!name) return '?';
        const parts = name.trim().split(/\s+/);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }

    /**
     * Generate color from string
     */
    function generateColor(str) {
        const colors = [
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
            'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
            'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
            'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
            'linear-gradient(135deg, #ff6e7f 0%, #bfe9ff 100%)'
        ];
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return colors[Math.abs(hash) % colors.length];
    }

    /**
     * Open chat from Instagram style list
     */
    window.openInstagramChat = function(conversationId, name) {
        // Switch to chats screen
        const chatsScreen = document.getElementById('chatsScreen');
        const homeScreen = document.getElementById('homeScreen');
        
        if (chatsScreen && homeScreen) {
            homeScreen.classList.remove('active');
            chatsScreen.classList.add('active');
            
            // Update navigation
            const navItems = document.querySelectorAll('.bottom-nav-item');
            navItems.forEach(nav => nav.classList.remove('active'));
            const chatsNav = document.querySelector('[data-screen="chats"]');
            if (chatsNav) {
                chatsNav.classList.add('active');
            }
        }

        // Load the conversation using the messaging.js function
        if (window.selectConversation) {
            window.selectConversation(conversationId);
        }
    };

    /**
     * Handle Logout
     */
    window.handleLogout = function() {
        if (confirm('Are you sure you want to log out?')) {
            // Clear auth token
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            
            // Redirect to login
            window.location.href = '/login';
        }
    };
})();

