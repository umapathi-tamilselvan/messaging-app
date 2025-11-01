<!-- Profile Screen -->
<div class="profile-screen">
    <!-- Header -->
    <header class="profile-header">
        <h1 class="profile-username" id="profileUsername">
            @auth
                {{ auth()->user()->name ?? 'username' }}
            @else
                username
            @endauth
        </h1>
        <button class="icon-button">
            <i class="bi bi-gear"></i>
        </button>
    </header>

    <!-- Profile Info -->
    <div class="profile-info">
        <div class="profile-avatar-large" id="profileAvatarLarge">
            <span id="profileAvatarText">
                @auth
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                @else
                    U
                @endauth
            </span>
        </div>
        <h2 class="profile-display-name" id="profileDisplayName">
            @auth
                {{ auth()->user()->name ?? 'Display Name' }}
            @else
                Display Name
            @endauth
        </h2>
        <p class="profile-bio" id="profileBio">
            @auth
                {{ auth()->user()->bio ?? 'No bio yet' }}
            @else
                No bio yet
            @endauth
        </p>

        <!-- Action Buttons -->
        <div class="profile-actions">
            <button class="profile-button primary" onclick="window.location.href='{{ route('profile') }}'">Edit Profile</button>
            <button class="profile-button secondary">Share Profile</button>
        </div>

        <!-- Stats -->
        <div class="profile-stats">
            <div class="profile-stat">
                <span class="profile-stat-value" id="postsCount">0</span>
                <span class="profile-stat-label">Posts</span>
            </div>
            <div class="profile-stat">
                <span class="profile-stat-value" id="friendsCount">0</span>
                <span class="profile-stat-label">Friends</span>
            </div>
            <div class="profile-stat">
                <span class="profile-stat-value" id="savedCount">0</span>
                <span class="profile-stat-label">Saved</span>
            </div>
        </div>
    </div>

    <!-- Settings List -->
    <div class="profile-settings">
        <div class="settings-item" onclick="window.location.href='{{ route('profile') }}'">
            <span>Account</span>
            <i class="bi bi-chevron-right"></i>
        </div>
        <div class="settings-item">
            <span>Privacy & Security</span>
            <i class="bi bi-chevron-right"></i>
        </div>
        <div class="settings-item">
            <span>Notifications</span>
            <i class="bi bi-chevron-right"></i>
        </div>
        <div class="settings-item">
            <span>Data Usage</span>
            <i class="bi bi-chevron-right"></i>
        </div>
        <div class="settings-item">
            <span>Help & Support</span>
            <i class="bi bi-chevron-right"></i>
        </div>
        <div class="settings-item">
            <span>About</span>
            <i class="bi bi-chevron-right"></i>
        </div>
        @auth
        <div class="settings-item logout" onclick="handleLogout()">
            <span>Log Out</span>
        </div>
        @endauth
    </div>
</div>
