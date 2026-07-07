<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaPreviewController extends Controller
{
    /**
     * Preview the media file securely by proxying it from storage.
     *
     * @param Media $media
     * @return mixed
     */
    public function __invoke(Media $media)
    {
        // 1. Authorization Control
        if (!auth()->check()) {
            abort(401, 'Unauthenticated.');
        }

        $user = auth()->user();

        // If the media is owned by a Submission, verify the ownership
        if ($media->mediable_type === Submission::class) {
            $submission = $media->mediable;
            if ($submission && $submission->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        }

        // 2. Storage Check
        $disk = Storage::disk($media->disk);
        if (!$disk->exists($media->path)) {
            abort(404, 'File not found.');
        }

        // 3. Stream preview with correct headers (inline content disposition)
        $stream = $disk->readStream($media->path);
        
        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            },
            200,
            [
                'Content-Type' => $media->mime ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . basename($media->original_name) . '"',
                'Cache-Control' => 'private, max-age=86400',
            ]
        );
    }
}
