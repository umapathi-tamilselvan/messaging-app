# Profile & Account Management Plan

## Overview
Complete profile setup and account management system for the messaging app with Arattai-inspired UI.

## Features to Implement

### 1. User Profile API Endpoints
- **GET /api/user** - Get current user profile
- **PUT /api/user/profile** - Update user profile (name, avatar)
- **PUT /api/user/avatar** - Update avatar only
- **PUT /api/user/status** - Update online/offline status

### 2. Profile Setup Flow
- **Onboarding**: After OTP verification, if user has no name, show profile setup modal/page
- **Required Fields**: 
  - Name (required, min 2 chars, max 50)
  - Avatar (optional, can skip)
- **Optional Fields**:
  - Bio/About (future)
  - Status message (future)

### 3. Account Settings Page
- **Profile Section**:
  - Display current profile (name, phone, avatar)
  - Edit name
  - Change avatar
  - View phone number (read-only)
  
- **Privacy Section**:
  - Last seen visibility (future)
  - Read receipts (future)
  
- **Security Section**:
  - Phone number (verified, read-only)
  - Change phone (future - requires OTP re-verification)
  
- **Data & Storage**:
  - Clear cache
  - Download data (future)
  
- **Account Actions**:
  - Logout
  - Delete account (future)

### 4. UI Components Needed
- Profile setup modal/page (shown after first login if name is empty)
- Account settings page/route
- Profile picture upload component
- Avatar display component with initials fallback
- Navigation link to settings (in dashboard header)

### 5. Implementation Steps
1. ✅ Create API endpoints for user profile management
2. ⏳ Create profile setup modal/page
3. ⏳ Create account settings page
4. ⏳ Add profile picture upload
5. ⏳ Add navigation and routing
6. ⏳ Update dashboard to show user profile
7. ⏳ Add profile completion check on login

### 6. Database Fields (Already Available)
- `name` - User's display name (nullable)
- `avatar_url` - Profile picture URL (nullable)
- `phone` - Phone number (required, unique)
- `status` - Online/offline status (enum)
- `last_seen` - Last active timestamp (nullable)
- `phone_verified_at` - Phone verification timestamp

### 7. UI/UX Flow

#### First Time User:
1. User logs in with OTP
2. Check if `name` is null/empty
3. Show "Complete Your Profile" modal/page
4. User enters name (and optionally uploads avatar)
5. Save profile → redirect to dashboard

#### Returning User:
1. User logs in → redirect to dashboard
2. Profile info shown in header/navbar
3. Settings accessible via profile menu/icon

### 8. Design Requirements
- Arattai-inspired clean UI
- Indian flag gradient accents
- Responsive design
- Avatar with initials fallback
- Smooth transitions and animations

