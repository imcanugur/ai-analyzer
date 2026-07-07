<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Actions\DownloadMediaAction;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaDownloadController extends Controller
{
    public function __construct(
        protected DownloadMediaAction $downloadMediaAction
    ) {}

    /**
     * Handle the download request securely.
     *
     * @param Media $media
     * @return StreamedResponse
     */
    public function __invoke(Media $media): StreamedResponse
    {
        return $this->downloadMediaAction->execute($media);
    }
}
