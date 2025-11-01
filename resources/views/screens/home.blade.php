<!-- Home Screen - Instagram Style Messages List -->
<div class="home-screen messages-list-screen">
    <div class="app-container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <span class="back-btn" id="backBtn" style="display: none;">←</span>
                    <span class="username" id="currentUsername">username</span>
                    <span class="dropdown-icon">▼</span>
                </div>
                <div class="header-icons">
                    <button class="icon-btn" id="newMessageBtn" data-bs-toggle="modal" data-bs-target="#newConversationModal" title="New Message">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
            </div>
            <div class="search-bar">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search" id="searchInput">
            </div>
        </div>

        <!-- Active Now Section -->
        <div class="active-now">
            <div class="active-now-header">Active Now</div>
            <div class="active-users" id="activeUsers">
                <!-- Active users will be loaded here -->
                <div class="active-user">
                    <div class="active-avatar">
                        <div class="active-avatar-inner">
                            <div class="avatar-content" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">B</div>
                        </div>
                        <span class="online-dot"></span>
                    </div>
                    <span class="active-name">bob_jones</span>
                </div>
                <div class="active-user">
                    <div class="active-avatar">
                        <div class="active-avatar-inner">
                            <div class="avatar-content" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">C</div>
                        </div>
                        <span class="online-dot"></span>
                    </div>
                    <span class="active-name">carol_w</span>
                </div>
                <div class="active-user">
                    <div class="active-avatar">
                        <div class="active-avatar-inner">
                            <div class="avatar-content" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">D</div>
                        </div>
                        <span class="online-dot"></span>
                    </div>
                    <span class="active-name">david_m</span>
                </div>
                <div class="active-user">
                    <div class="active-avatar">
                        <div class="active-avatar-inner">
                            <div class="avatar-content" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">E</div>
                        </div>
                        <span class="online-dot"></span>
                    </div>
                    <span class="active-name">emma_lee</span>
                </div>
                <div class="active-user">
                    <div class="active-avatar">
                        <div class="active-avatar-inner">
                            <div class="avatar-content" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">F</div>
                        </div>
                        <span class="online-dot"></span>
                    </div>
                    <span class="active-name">frank_s</span>
                </div>
            </div>
        </div>

        <!-- Messages Label -->
        <div class="messages-section">Messages</div>

        <!-- Chat List -->
        <div class="chat-list" id="chatList">
            <!-- Chat items will be loaded here -->
            <div class="text-center py-5" id="loadingChats">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle">
        <i class="bi bi-moon-fill"></i>
    </button>
</div>

<!-- New Conversation Modal -->
<div class="modal fade" id="newConversationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--radius-lg); border: none; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="border-bottom: 1px solid var(--moment-border); padding: 16px 20px;">
                <h5 class="modal-title fw-bold" style="color: var(--moment-text-primary); font-size: 16px;">New Conversation</h5>
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
