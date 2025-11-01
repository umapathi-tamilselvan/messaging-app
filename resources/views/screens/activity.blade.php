<!-- Activity Screen -->
<div class="activity-screen">
    <!-- Header -->
    <header class="activity-header">
        <h1 class="activity-title">Activity</h1>
    </header>

    <!-- Tabs -->
    <div class="activity-tabs">
        <button class="activity-tab active" data-tab="following">Following</button>
        <button class="activity-tab" data-tab="you">You</button>
    </div>

    <!-- Activities List -->
    <div class="activities-list" id="activitiesList">
        <!-- Activities will be loaded dynamically -->
        <div class="empty-state" style="display: none; padding: 3rem 1rem; text-align: center;">
            <i class="bi bi-bell" style="font-size: 3rem; color: var(--moment-text-secondary); opacity: 0.5;"></i>
            <p style="color: var(--moment-text-secondary); margin-top: 1rem;">No activity yet</p>
        </div>
    </div>
</div>
