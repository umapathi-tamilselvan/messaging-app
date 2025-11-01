@extends('layouts.app')

@section('title', 'My Profile - Messaging App')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="bi bi-person-circle"></i> My Profile
                    </h2>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Profile Picture -->
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                     alt="Profile Picture" 
                                     class="img-thumbnail rounded-circle mb-3"
                                     style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width: 200px; height: 200px; font-size: 4rem;">
                                    <i class="bi bi-person"></i>
                                </div>
                            @endif
                        </div>

                        <!-- User Information -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-person"></i> Full Name
                                </label>
                                <h4 class="mb-0">{{ $user->name }}</h4>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <p class="mb-0">{{ $user->email }}</p>
                            </div>

                            @if($user->phone)
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-telephone"></i> Phone Number
                                </label>
                                <p class="mb-0">{{ $user->phone }}</p>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-chat-quote"></i> Status
                                </label>
                                <p class="mb-0 fst-italic">{{ $user->status }}</p>
                            </div>

                            @if($user->bio)
                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-file-text"></i> Bio
                                </label>
                                <p class="mb-0">{{ $user->bio }}</p>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label text-muted">
                                    <i class="bi bi-calendar"></i> Member Since
                                </label>
                                <p class="mb-0">{{ $user->created_at->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('landing') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-house"></i> Back to Home
                                </a>
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square"></i> Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

