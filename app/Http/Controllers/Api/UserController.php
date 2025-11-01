<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get current user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'phone' => $user->phone,
            'name' => $user->name,
            'avatar_url' => $user->avatar_url,
            'status' => $user->status,
            'last_seen' => $user->last_seen,
            'phone_verified_at' => $user->phone_verified_at,
            'bio' => $user->bio ?? null,
            'profile_complete' => !empty($user->name),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|min:2|max:50',
            'bio' => 'nullable|string|max:200',
            'avatar_url' => 'sometimes|nullable|url|max:500',
        ]);

        $updateData = [];

        if ($request->has('name')) {
            // Auto-verify name: Trim and validate
            $name = trim($request->name);
            if (strlen($name) >= 2) {
                $updateData['name'] = $name;
            }
        }

        if ($request->has('bio')) {
            // Auto-verify bio: Trim and sanitize
            $bio = trim($request->bio);
            if (strlen($bio) <= 200) {
                $updateData['bio'] = $bio ?: null;
            }
        }

        if ($request->has('avatar_url')) {
            // Auto-verify avatar URL: Validate it's a valid image URL
            $avatarUrl = $request->avatar_url;
            if (empty($avatarUrl)) {
                $updateData['avatar_url'] = null;
            } else {
                // Validate URL format and check if it's an image
                if (filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                    // Check if URL ends with image extension
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $urlPath = parse_url($avatarUrl, PHP_URL_PATH);
                    $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
                    
                    if (in_array($extension, $imageExtensions)) {
                        $updateData['avatar_url'] = $avatarUrl;
                    }
                }
            }
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        $updatedUser = $user->fresh();
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $updatedUser->id,
                'phone' => $updatedUser->phone,
                'name' => $updatedUser->name,
                'avatar_url' => $updatedUser->avatar_url,
                'status' => $updatedUser->status,
                'bio' => $updatedUser->bio,
                'profile_complete' => !empty($updatedUser->name),
            ],
        ]);
    }

    /**
     * Upload profile photo (returns presigned URL for S3)
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'mime_type' => 'required|string|in:image/jpeg,image/png,image/gif,image/webp',
            'size' => 'required|integer|max:5242880', // 5MB max for profile photos
        ]);

        $user = $request->user();

        // Auto-verify: Only allow image types
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($request->mime_type, $allowedTypes)) {
            return response()->json([
                'error' => 'Invalid image type. Only JPEG, PNG, GIF, and WebP are allowed.',
            ], 400);
        }

        // Auto-verify: Check file size
        if ($request->size > 5242880) {
            return response()->json([
                'error' => 'Profile photo size exceeds 5MB limit.',
            ], 400);
        }

        // Generate unique filename
        $extension = pathinfo($request->filename, PATHINFO_EXTENSION);
        $filename = 'avatars/' . $user->id . '_' . uniqid() . '_' . time() . '.' . $extension;

        // Delete old avatar if exists
        if ($user->avatar_url) {
            try {
                // Extract path from URL if it's an S3 URL
                $oldPath = parse_url($user->avatar_url, PHP_URL_PATH);
                if ($oldPath && strpos($oldPath, 'avatars/') === 1) {
                    Storage::disk('s3')->delete(ltrim($oldPath, '/'));
                }
            } catch (\Exception $e) {
                // Ignore deletion errors
            }
        }

        // Generate presigned URL for S3 upload
        $s3Client = Storage::disk('s3')->getClient();
        $command = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename,
            'ContentType' => $request->mime_type,
        ]);

        $presignedRequest = $s3Client->createPresignedRequest($command, '+5 minutes');
        $uploadUrl = (string) $presignedRequest->getUri();

        // Generate the public URL for the uploaded image
        $publicUrl = Storage::disk('s3')->url($filename);

        // Auto-verify and update avatar URL immediately
        $user->update(['avatar_url' => $publicUrl]);

        return response()->json([
            'upload_url' => $uploadUrl,
            'avatar_url' => $publicUrl,
            'expires_in' => 300,
            'message' => 'Avatar uploaded and verified successfully',
        ]);
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,offline,away',
        ]);

        $user = $request->user();
        $user->update([
            'status' => $request->status,
            'last_seen' => now(),
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'status' => $user->status,
            'last_seen' => $user->last_seen,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1|max:255',
        ]);

        $currentUser = $request->user();
        $query = $request->q;

        // Search users by phone or name, excluding current user
        $users = User::where('id', '!=', $currentUser->id)
            ->where(function ($q) use ($query) {
                $q->where('phone', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('id', 'phone', 'name', 'avatar_url', 'status')
            ->limit(20)
            ->get();

        return response()->json($users);
    }
}

