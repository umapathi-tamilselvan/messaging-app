@extends('layouts.app')

@section('title', 'Edit Profile - Messaging App')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </h2>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Profile Picture -->
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <label class="form-label d-block">
                                    <i class="bi bi-image"></i> Profile Picture
                                </label>
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                         alt="Current Profile Picture" 
                                         id="currentProfilePicture"
                                         class="img-thumbnail rounded-circle mb-3"
                                         style="width: 200px; height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                         id="currentProfilePicturePlaceholder"
                                         style="width: 200px; height: 200px; font-size: 4rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                @endif
                                <input type="file" 
                                       class="form-control @error('profile_picture') is-invalid @enderror" 
                                       id="profile_picture" 
                                       name="profile_picture"
                                       accept="image/*">
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted d-block mt-2">Upload new profile picture (Max 2MB)</small>
                                <div id="profilePreview" class="mt-2"></div>
                            </div>

                            <!-- User Information -->
                            <div class="col-md-8">
                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person"></i> Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope"></i> Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="bi bi-telephone"></i> Phone Number
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="+91 1234567890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Include country code (e.g., +91 for India)</small>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        <i class="bi bi-chat-quote"></i> Status
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('status') is-invalid @enderror" 
                                           id="status" 
                                           name="status" 
                                           value="{{ old('status', $user->status) }}"
                                           placeholder="Hey there! I am using Messaging App">
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bio -->
                                <div class="mb-3">
                                    <label for="bio" class="form-label">
                                        <i class="bi bi-file-text"></i> Bio
                                    </label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" 
                                              name="bio" 
                                              rows="3"
                                              placeholder="Tell us about yourself (optional)">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Profile picture preview
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail rounded-circle" style="max-width: 200px; max-height: 200px; object-fit: cover;" alt="Profile Preview">';
                
                // Hide current picture if exists
                const currentPic = document.getElementById('currentProfilePicture');
                const currentPlaceholder = document.getElementById('currentProfilePicturePlaceholder');
                if (currentPic) currentPic.style.display = 'none';
                if (currentPlaceholder) currentPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            // Show current picture again if file input is cleared
            const preview = document.getElementById('profilePreview');
            preview.innerHTML = '';
            const currentPic = document.getElementById('currentProfilePicture');
            const currentPlaceholder = document.getElementById('currentProfilePicturePlaceholder');
            if (currentPic) currentPic.style.display = 'block';
            if (currentPlaceholder) currentPlaceholder.style.display = 'flex';
        }
    });
</script>
@endpush

