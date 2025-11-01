<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
        ]);

        $phone = $request->phone;
        $key = 'otp:' . $phone;

        // Rate limiting: 3 OTPs per hour per phone
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'phone' => "Too many OTP requests. Please try again in {$seconds} seconds.",
            ]);
        }

        RateLimiter::hit($key, 3600); // 1 hour

        // Generate OTP
        $otp = Otp::generate($phone);

        // In production, send SMS via provider (Twilio, AWS SNS, etc.)
        // For now, we'll log it or return it in development
        if (config('app.debug')) {
            \Log::info("OTP for {$phone}: {$otp->code}");
        }

        return response()->json([
            'success' => true,
            'expires_in' => 300, // 5 minutes
            'message' => config('app.debug') ? "OTP: {$otp->code}" : 'OTP sent successfully',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $otp = Otp::where('phone', $request->phone)
            ->where('code', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            // Increment attempts on invalid OTP
            $invalidOtp = Otp::where('phone', $request->phone)
                ->where('code', $request->otp)
                ->where('used', false)
                ->first();
            
            if ($invalidOtp) {
                $invalidOtp->incrementAttempts();
            }

            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired OTP.',
            ]);
        }

        // Mark OTP as used
        $otp->markAsUsed();

        // Find or create user
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'phone_verified_at' => now(),
                'status' => 'offline',
            ]
        );

        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->only(['id', 'phone', 'name', 'avatar_url', 'status', 'last_seen']),
        ]);
    }
}
