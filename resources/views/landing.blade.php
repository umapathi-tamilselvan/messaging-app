@extends('layouts.app')

@section('title', 'Welcome - Messaging App')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-chat-dots-fill text-primary" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="display-4 mb-3 fw-bold text-primary">Welcome to Messaging App</h1>
                    <p class="lead text-muted mb-5">Connect with your friends and family instantly. Start your messaging journey today!</p>
                    
                    <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-person-plus"></i> Get Started
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <i class="bi bi-shield-check text-success" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Secure</h5>
                                <p class="text-muted small">End-to-end encryption</p>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <i class="bi bi-lightning-charge text-warning" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Fast</h5>
                                <p class="text-muted small">Instant messaging</p>
                            </div>
                            <div class="col-md-4">
                                <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Social</h5>
                                <p class="text-muted small">Connect with everyone</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .min-vh-100 {
        min-height: 80vh;
    }
</style>
@endpush
@endsection

