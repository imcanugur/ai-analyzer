<?php

namespace App\Actions;

use App\Models\Media;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadMediaAction
{
    /**
     * Execute the action to download a media file securely.
     */
    public function execute(Media $media): StreamedResponse
    {
        // 1. Authorization Control
        if (! auth()->check()) {
            abort(401, 'Unauthenticated.');
        }

        $user = auth()->user();

        // If the media is owned by a Submission, verify the ownership
        if ($media->mediable_type === Submission::class) {
            $submission = $media->mediable;
            if ($submission && $submission->user_id !== $user->id) {
                abort(403, 'Unauthorized access to this media file.');
            }
        }

        // 2. Storage Check
        $disk = Storage::disk($media->disk);
        if (! $disk->exists($media->path)) {
            abort(404, 'The requested file does not exist on storage.');
        }

        // 3. Forced Response Attachment stream download
        return Storage::disk($media->disk)->download($media->path, $media->original_name);
    }
}
