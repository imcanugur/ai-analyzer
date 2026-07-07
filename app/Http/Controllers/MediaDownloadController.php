<?php

namespace App\Http\Controllers;

use App\Actions\DownloadMediaAction;
use App\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaDownloadController extends Controller
{
    public function __construct(
        protected DownloadMediaAction $downloadMediaAction
    ) {}

    /**
     * Handle the download request securely.
     */
    public function __invoke(Media $media): StreamedResponse
    {
        return $this->downloadMediaAction->execute($media);
    }
}
