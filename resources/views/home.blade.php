@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section - Instagram Style -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold mb-4 animate-fade-in" style="color: var(--ig-gray-600);">
                    Connect. Communicate. Collaborate.
                </h1>
                <p class="lead mb-4 animate-fade-in-delay-1" style="color: var(--ig-gray-500);">
                    Experience seamless messaging with real-time updates, file sharing, and secure conversations. 
                    Stay connected with your team and friends like never before.
                </p>
                <div class="d-flex flex-wrap gap-3 animate-fade-in-delay-2">
                    <a href="#auth-section" class="btn btn-primary px-4 py-3" style="border-radius: var(--border-radius); font-size: 14px; font-weight: 600;">
                        <i class="bi bi-rocket-takeoff me-2"></i>Get Started Free
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-3" style="border-radius: var(--border-radius); font-size: 14px; font-weight: 600;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                    <a href="#features" class="btn btn-outline-secondary px-4 py-3" style="border-radius: var(--border-radius); font-size: 14px; font-weight: 600;">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image-wrapper">
                    <div class="hero-message-card message-card-1">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar-sm bg-success rounded-circle me-2"></div>
                            <div>
                                <small class="fw-semibold">Sarah</small>
                                <small class="text-muted d-block">2 mins ago</small>
                            </div>
                        </div>
                        <p class="mb-0">Hey! How's the project going?</p>
                    </div>
                    <div class="hero-message-card message-card-2">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar-sm bg-primary rounded-circle me-2"></div>
                            <div>
                                <small class="fw-semibold">You</small>
                                <small class="text-muted d-block">Just now</small>
                            </div>
                        </div>
                        <p class="mb-0">Going great! We're ahead of schedule 🚀</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5" style="background: var(--ig-gray-50);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3" style="color: var(--ig-gray-600);">Powerful Features</h2>
            <p class="lead text-muted" style="color: var(--ig-gray-500);">Everything you need for modern communication</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-lightning-charge fs-2"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Real-Time Messaging</h4>
                        <p class="text-muted">Send and receive messages instantly with real-time synchronization across all devices.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-shield-check fs-2"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Secure & Private</h4>
                        <p class="text-muted">End-to-end encryption and secure authentication ensure your conversations stay private.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-people fs-2"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Group Chats</h4>
                        <p class="text-muted">Create groups, share files, and collaborate with teams of any size effortlessly.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-paperclip fs-2"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">File Sharing</h4>
                        <p class="text-muted">Share images, documents, and files seamlessly with support for all file types.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-check2-all fs-2"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Read Receipts</h4>
                        <p class="text-muted">Know when your messages are delivered and read with intelligent status indicators.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-purple bg-opacity-10 text-purple rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="bi bi-device-ssd fs-2"></i>
                        </div>
                        <h4 class="fw-semibold mb-3">Cross-Platform</h4>
                        <p class="text-muted">Access your messages from any device - desktop, tablet, or mobile - anywhere, anytime.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Authentication Section -->
<section id="auth-section" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h2 class="display-4 fw-bold mb-3" style="color: var(--ig-gray-600);">Get Started in Seconds</h2>
                    <p class="lead text-muted" style="color: var(--ig-gray-500);">Quick and secure authentication with phone verification. No passwords needed!</p>
                </div>
                
                <div class="row g-4">
                    <!-- Register Card -->
                    <div class="col-md-6">
                        <div class="auth-card card h-100 shadow-lg hover-lift">
                            <div class="card-body p-5 text-center">
                                <div class="auth-icon-wrapper mb-4">
                                    <i class="bi bi-person-plus-fill text-primary fs-1"></i>
                                </div>
                                <h3 class="fw-bold mb-3">New User?</h3>
                                <p class="text-muted mb-4">
                                    Create your account instantly with just your phone number. 
                                    No email or password required!
                                </p>
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Quick phone verification
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Instant account creation
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Secure OTP authentication
                                    </li>
                                    <li>
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        No personal information needed
                                    </li>
                                </ul>
                                <a href="{{ route('login') }}?action=register" class="btn btn-primary btn-lg w-100 rounded-pill">
                                    <i class="bi bi-person-plus me-2"></i>Register Now
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Login Card -->
                    <div class="col-md-6">
                        <div class="auth-card card h-100 shadow-lg hover-lift">
                            <div class="card-body p-5 text-center">
                                <div class="auth-icon-wrapper mb-4">
                                    <i class="bi bi-box-arrow-in-right text-success fs-1"></i>
                                </div>
                                <h3 class="fw-bold mb-3">Already Registered?</h3>
                                <p class="text-muted mb-4">
                                    Sign in to your account using your verified phone number. 
                                    Access all your conversations instantly.
                                </p>
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        One-tap phone login
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Access all conversations
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Secure OTP verification
                                    </li>
                                    <li>
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Fast and convenient
                                    </li>
                                </ul>
                                <a href="{{ route('login') }}?action=login" class="btn btn-success btn-lg w-100 rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <div class="alert alert-info d-inline-block mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Both login and registration use the same secure OTP verification process. 
                        New users are automatically registered when they verify their phone number.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section - Instagram Style -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-3">Ready to get started?</h2>
                <p class="lead mb-0">Join thousands of users who trust our platform for their communication needs.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5" style="border-radius: var(--border-radius); font-size: 14px; font-weight: 600;">
                    <i class="bi bi-arrow-right-circle me-2"></i>Start Messaging
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

