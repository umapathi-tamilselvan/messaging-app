<!-- Chats Screen - Same as Home (Duplicate for navigation) -->
<div class="chats-screen">
    <!-- Full Chat Interface -->
    <div class="chats-screen-full">
        <!-- Sidebar - Conversations List -->
        <div class="chats-sidebar" id="chatsSidebarChats">
            <!-- Header -->
            <div class="chats-sidebar-header">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0 fw-bold" style="color: var(--moment-text-primary); font-size: 16px;">Messages</h5>
                    <button class="btn btn-sm rounded-circle" id="newConversationBtnChats" data-bs-toggle="modal" data-bs-target="#newConversationModal" title="New Conversation" style="background: transparent; color: var(--moment-text-primary); border: none; width: 32px; height: 32px; padding: 0;">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="input-group" style="border-radius: var(--radius-md); overflow: hidden; background: var(--moment-surface); border: none;">
                    <span class="input-group-text bg-transparent border-0" style="background: transparent !important;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-0" id="searchConversationsChats" placeholder="Search" style="background: transparent !important; font-size: 14px; padding: 8px 0;">
                </div>
            </div>

            <!-- Conversations List -->
            <div class="chats-conversations-list" id="conversationsListChats">
                <div class="text-center py-5">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chats-main-area" id="chatsMainAreaChats">
            <!-- Empty State -->
            <div class="chats-empty-state" id="emptyChatStateChats">
                <div class="empty-chat-icon mb-4">
                    <i class="bi bi-chat-left-text" style="font-size: 5rem; color: var(--moment-text-secondary); opacity: 0.5;"></i>
                </div>
                <h4 class="fw-semibold mb-2" style="color: var(--moment-text-primary); font-size: 20px;">Select a conversation</h4>
                <p class="text-muted mb-4" style="color: var(--moment-text-secondary); font-size: 14px;">Choose a conversation from the sidebar to start messaging</p>
                <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#newConversationModal" style="border-radius: var(--radius-md); font-size: 14px; font-weight: 600;">
                    <i class="bi bi-plus-circle me-2"></i>Start New Conversation
                </button>
            </div>

            <!-- Active Chat (Hidden by default) -->
            <div class="chats-active-chat d-none" id="activeChatChats">
                <!-- Chat Header -->
                <div class="chats-chat-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="conversation-avatar me-3" id="chatAvatarChats" style="width: 40px; height: 40px;">
                                <span class="d-flex align-items-center justify-content-center h-100 text-white fw-bold" id="chatAvatarTextChats" style="font-size: 16px;"></span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold" id="chatTitleChats" style="color: var(--moment-text-primary); font-size: 14px; font-weight: 600;"></h6>
                                <small class="text-muted" id="chatSubtitleChats" style="color: var(--moment-text-secondary); font-size: 12px;"></small>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm rounded-circle" id="chatInfoBtnChats" title="Chat Info" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: transparent; border: none; color: var(--moment-text-primary);">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div class="chats-messages-container" id="messagesContainerChats">
                    <div id="messagesListChats"></div>
                </div>

                <!-- Message Input -->
                <div class="chats-message-input">
                    <form id="messageFormChats">
                        <div class="input-group" style="background: var(--moment-surface); border-radius: var(--radius-xl); padding: 4px;">
                            <button type="button" class="btn border-0" id="attachBtnChats" title="Attach file" style="background: transparent; color: var(--moment-text-primary); padding: 8px 12px;">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <input type="text" class="form-control border-0" id="messageInputChats" placeholder="Message..." autocomplete="off" style="background: transparent; font-size: 14px; padding: 8px 0;">
                            <button type="button" class="btn border-0" id="emojiBtnChats" title="Add emoji" style="background: transparent; color: var(--moment-text-primary); padding: 8px 12px;">
                                <i class="bi bi-emoji-smile"></i>
                            </button>
                            <button type="submit" class="btn border-0" id="sendBtnChats" style="padding: 0; margin: 0 4px;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                    <input type="file" id="fileInputChats" class="d-none" multiple>
                </div>
            </div>
        </div>
    </div>
</div>
