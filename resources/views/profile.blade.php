@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-circle me-2"></i>Profile Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Profile Photo Section -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <div id="avatarPreview" class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px; font-size: 3rem; color: white; cursor: pointer; overflow: hidden; border: 4px solid var(--gray-200);">
                                <span id="avatarInitials">?</span>
                                <img id="avatarImage" src="" alt="Avatar" class="d-none w-100 h-100" style="object-fit: cover;">
                            </div>
                            <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                                    style="width: 36px; height: 36px; padding: 0;" id="changeAvatarBtn" title="Change Photo">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                        </div>
                        <input type="file" id="avatarInput" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
                        <p class="text-muted mt-2 mb-0">
                            <small>Click to change profile photo<br>Max 5MB • JPEG, PNG, GIF, WebP</small>
                        </p>
                    </div>

                    <!-- Profile Form -->
                    <form id="profileForm">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required minlength="2" maxlength="50" 
                                   placeholder="Enter your name">
                            <div class="form-text">Your display name (2-50 characters)</div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" readonly 
                                   style="background-color: var(--gray-100);">
                            <div class="form-text">Verified phone number (cannot be changed)</div>
                        </div>

                        <div class="mb-4">
                            <label for="bio" class="form-label fw-semibold">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3" maxlength="200" 
                                     placeholder="Tell us about yourself (optional)"></textarea>
                            <div class="form-text">
                                <span id="bioCharCount">0</span>/200 characters
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Save Profile
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@push('scripts')
<script>
    let currentUser = null;
    
    // Load current user profile
    async function loadProfile() {
        try {
            const response = await axios.get('/user');
            currentUser = response.data;
            
            // Update form fields
            document.getElementById('name').value = currentUser.name || '';
            document.getElementById('phone').value = currentUser.phone || '';
            document.getElementById('bio').value = currentUser.bio || '';
            
            // Update avatar
            updateAvatarDisplay(currentUser.avatar_url, currentUser.name);
            
            // Update bio character count
            updateBioCharCount();
        } catch (error) {
            console.error('Error loading profile:', error);
            window.utils.showNotification('Failed to load profile', 'error');
        }
    }
    
    // Update avatar display
    function updateAvatarDisplay(avatarUrl, name) {
        const avatarImage = document.getElementById('avatarImage');
        const avatarInitials = document.getElementById('avatarInitials');
        
        if (avatarUrl) {
            avatarImage.src = avatarUrl;
            avatarImage.classList.remove('d-none');
            avatarInitials.classList.add('d-none');
        } else {
            avatarImage.classList.add('d-none');
            avatarInitials.classList.remove('d-none');
            avatarInitials.textContent = window.utils.getInitials(name || '?');
            const color = window.utils.generateColor(name || 'User');
            document.getElementById('avatarPreview').style.backgroundColor = color;
        }
    }
    
    // Bio character count
    function updateBioCharCount() {
        const bio = document.getElementById('bio').value;
        document.getElementById('bioCharCount').textContent = bio.length;
    }
    
    // Handle avatar upload
    document.getElementById('changeAvatarBtn').addEventListener('click', () => {
        document.getElementById('avatarInput').click();
    });
    
    document.getElementById('avatarInput').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Auto-verify file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            window.utils.showNotification('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.', 'error');
            return;
        }
        
        // Auto-verify file size (5MB)
        if (file.size > 5242880) {
            window.utils.showNotification('File size exceeds 5MB limit.', 'error');
            return;
        }
        
        try {
            // Show loading state
            const btn = document.getElementById('changeAvatarBtn');
            btn.disabled = true;
            btn.innerHTML = '<div class="spinner-border spinner-border-sm"></div>';
            
            // Get presigned URL
            const signResponse = await axios.post('/user/avatar', {
                filename: file.name,
                mime_type: file.type,
                size: file.size
            });
            
            // Upload to S3
            await axios.put(signResponse.data.upload_url, file, {
                headers: {
                    'Content-Type': file.type
                }
            });
            
            // Update avatar display
            updateAvatarDisplay(signResponse.data.avatar_url, currentUser?.name);
            currentUser.avatar_url = signResponse.data.avatar_url;
            
            window.utils.showNotification('Profile photo uploaded and verified successfully!', 'success');
            
            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-camera-fill"></i>';
        } catch (error) {
            console.error('Error uploading avatar:', error);
            window.utils.showNotification('Failed to upload profile photo: ' + (error.response?.data?.message || error.message), 'error');
            
            const btn = document.getElementById('changeAvatarBtn');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-camera-fill"></i>';
        }
        
        // Reset input
        e.target.value = '';
    });
    
    // Bio character counter
    document.getElementById('bio').addEventListener('input', updateBioCharCount);
    
    // Submit profile form
    document.getElementById('profileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value.trim();
        const bio = document.getElementById('bio').value.trim();
        
        // Auto-verify name
        if (name.length < 2) {
            window.utils.showNotification('Name must be at least 2 characters', 'warning');
            return;
        }
        
        // Auto-verify bio
        if (bio.length > 200) {
            window.utils.showNotification('Bio must not exceed 200 characters', 'warning');
            return;
        }
        
        try {
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            
            const response = await axios.put('/user/profile', {
                name: name,
                bio: bio || null
            });
            
            // Update current user
            currentUser = response.data.user;
            
            // Update localStorage
            localStorage.setItem('user', JSON.stringify(currentUser));
            
            window.utils.showNotification('Profile updated and verified successfully!', 'success');
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Save Profile';
        } catch (error) {
            console.error('Error updating profile:', error);
            window.utils.showNotification('Failed to update profile: ' + (error.response?.data?.message || error.message), 'error');
            
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Save Profile';
        }
    });
    
    // Load profile on page load
    loadProfile();
</script>
@endpush

