@extends('layouts.moment')

@section('title', 'Moment - Chats')

@section('content')
<div class="moment-app">
    <!-- Home Screen - Instagram Style Messages List -->
    <div id="homeScreen" class="screen active">
        @include('screens.home')
    </div>

    <!-- Search Screen -->
    <div id="searchScreen" class="screen">
        <div class="home-screen">
            <div class="home-header">
                <h1 class="home-title">Search</h1>
            </div>
            <div class="text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">Search functionality coming soon</p>
            </div>
        </div>
    </div>

    <!-- Camera Screen -->
    <div id="cameraScreen" class="screen">
        @include('screens.camera')
    </div>

    <!-- Activity Screen -->
    <div id="activityScreen" class="screen">
        @include('screens.activity')
    </div>

    <!-- Messages/Chats Screen - Full Chat Interface -->
    <div id="chatsScreen" class="screen">
        @include('screens.chats')
    </div>

    <!-- Profile Screen -->
    <div id="profileScreen" class="screen">
        @include('screens.profile')
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/moment.css') }}">
<link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
@endpush

@push('scripts')
<!-- Pusher for real-time messaging -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // API_BASE_URL is not needed - axios.defaults.baseURL is already set to '/api' in app.js
    const AUTH_TOKEN = localStorage.getItem('auth_token');
    
    // Set up Axios default headers
    if (AUTH_TOKEN) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${AUTH_TOKEN}`;
    }
    
    // Pusher configuration (fallback to polling if Pusher not configured)
    window.PUSHER_CONFIG = {
        enabled: {{ env('BROADCAST_DRIVER') === 'pusher' && env('PUSHER_APP_KEY') ? 'true' : 'false' }},
        key: '{{ env('PUSHER_APP_KEY', '') }}',
        cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
        encrypted: true,
        authEndpoint: '{{ url('/broadcasting/auth') }}',
        auth: {
            headers: {
                'Authorization': `Bearer ${AUTH_TOKEN}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }
    };
</script>
<script src="{{ asset('js/moment-app.js') }}"></script>
<script src="{{ asset('js/messaging.js') }}"></script>
@endpush
