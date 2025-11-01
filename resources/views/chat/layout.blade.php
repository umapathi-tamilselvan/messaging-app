@extends('layouts.app')

@section('title', 'Chats - Messaging App')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endpush

@section('content')
<div class="container-fluid p-0 chat-container">
    <!-- Header -->
    <nav class="navbar navbar-light bg-white shadow-sm border-bottom px-3">
        <span class="navbar-brand mb-0 h5 d-flex align-items-center">
            <i class="bi bi-chat-dots-fill text-primary me-2"></i> Messaging App
        </span>
        <div class="ms-auto d-flex align-items-center gap-2">
            <a href="{{ route('chats.index') }}" class="btn btn-link text-secondary text-decoration-none"><i class="bi bi-chat-text"></i> Chats</a>
            <a href="{{ route('profile.show') }}" class="btn btn-link text-secondary text-decoration-none"><i class="bi bi-person-circle"></i> Profile</a>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-link text-danger text-decoration-none"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <div class="row g-0 flex-grow-1" style="height: calc(100vh - 57px);">
        <!-- Sidebar -->
        <aside class="col-12 col-md-4 col-lg-3 border-end bg-white chat-sidebar-wrapper">
            @include('chat.sidebar', ['user' => $user ?? null])
        </aside>

        <!-- Chat Area -->
        <main class="col-12 col-md-8 col-lg-9 chat-main-wrapper">
            @yield('chat-content')
        </main>
    </div>
</div>
@endsection
