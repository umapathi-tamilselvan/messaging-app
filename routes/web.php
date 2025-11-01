<?php

use Illuminate\Support\Facades\Route;

// Home/Landing Page
Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication Routes
Route::get('/login', function () {
    // Redirect to dashboard if already authenticated
    if (request()->cookie('auth_token')) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

// Dashboard Route (protected by middleware or client-side check)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Profile/Settings Route
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
