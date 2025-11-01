/* ===================================
   Main Application JavaScript
   =================================== */

// Set up CSRF token for Axios
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Set up API base URL
axios.defaults.baseURL = '/api';

// Handle logout and auth state
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                window.location.href = '/login';
            }
        });
    }
    
    // Check if user is authenticated and update navbar
    const authToken = localStorage.getItem('auth_token');
    if (authToken) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
        
        // Show dashboard/profile/logout, hide login
        const loginNavItem = document.getElementById('loginNavItem');
        const dashboardNavItem = document.getElementById('dashboardNavItem');
        const profileNavItem = document.getElementById('profileNavItem');
        const logoutNavItem = document.getElementById('logoutNavItem');
        
        if (loginNavItem) loginNavItem.classList.add('d-none');
        if (dashboardNavItem) dashboardNavItem.classList.remove('d-none');
        if (profileNavItem) profileNavItem.classList.remove('d-none');
        if (logoutNavItem) logoutNavItem.classList.remove('d-none');
    }
});

// Utility Functions
const utils = {
    formatTime: function(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        if (days > 7) {
            return date.toLocaleDateString();
        } else if (days > 0) {
            return `${days}d ago`;
        } else if (hours > 0) {
            return `${hours}h ago`;
        } else if (minutes > 0) {
            return `${minutes}m ago`;
        } else {
            return 'Just now';
        }
    },
    
    formatFullTime: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString();
    },
    
    getInitials: function(name) {
        if (!name) return '?';
        const parts = name.trim().split(' ');
        if (parts.length === 1) {
            return parts[0].substring(0, 2).toUpperCase();
        }
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    },
    
    generateColor: function(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        const colors = [
            '#0d6efd', '#198754', '#dc3545', '#ffc107', '#0dcaf0',
            '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'
        ];
        return colors[Math.abs(hash) % colors.length];
    },
    
    showNotification: function(message, type = 'info') {
        // Simple notification - can be enhanced with toast library
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
};

// Export utils for use in other scripts
window.utils = utils;

