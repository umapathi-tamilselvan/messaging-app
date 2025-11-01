<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function sign(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'mime_type' => 'required|string',
            'size' => 'required|integer|max:52428800', // 50MB max
        ]);

        // Validate file size by type
        $maxSizes = [
            'image' => 10 * 1024 * 1024, // 10MB
            'video' => 50 * 1024 * 1024, // 50MB
            'file' => 25 * 1024 * 1024,  // 25MB
        ];

        $fileType = explode('/', $request->mime_type)[0];
        $maxSize = $maxSizes[$fileType] ?? $maxSizes['file'];

        if ($request->size > $maxSize) {
            return response()->json([
                'error' => "File size exceeds maximum allowed size for {$fileType}",
            ], 400);
        }

        // Generate unique filename
        $extension = pathinfo($request->filename, PATHINFO_EXTENSION);
        $filename = 'attachments/' . uniqid() . '_' . time() . '.' . $extension;

        // Create attachment record
        $attachment = Attachment::create([
            'message_id' => null, // Will be updated when message is created
            'path' => $filename,
            'mime_type' => $request->mime_type,
            'size' => $request->size,
        ]);

        // Generate presigned URL for S3 upload
        $s3Client = Storage::disk('s3')->getClient();
        $command = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename,
            'ContentType' => $request->mime_type,
        ]);

        $presignedRequest = $s3Client->createPresignedRequest($command, '+5 minutes');

        return response()->json([
            'upload_url' => (string) $presignedRequest->getUri(),
            'attachment_id' => $attachment->id,
            'expires_in' => 300,
        ]);
    }
}
