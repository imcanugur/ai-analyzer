<?php

namespace App\Services;

use App\Contracts\MediaRepositoryInterface;
use App\Contracts\MediaTypeResolver;
use App\Contracts\PathGenerator;
use App\Events\MediaCreated;
use App\Events\MediaCreating;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(
        protected PathGenerator $pathGenerator,
        protected MediaTypeResolver $mediaTypeResolver,
        protected MediaRepositoryInterface $mediaRepository
    ) {}

    /**
     * Store a file (UploadedFile or pre-stored path) and attach it to a given model.
     */
    public function createMedia(Model $model, UploadedFile|string $file, string $disk = 'r2', ?string $originalName = null): Media
    {
        if ($file instanceof UploadedFile) {
            return $this->createFromFile($model, $file, $disk);
        }

        return $this->createFromStoredPath($model, $file, $disk, $originalName);
    }

    /**
     * Store an uploaded file and attach it as a Media model to a given parent model.
     */
    public function createFromFile(Model $model, UploadedFile $file, string $disk = 'r2'): Media
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension();
        $mime = $file->getMimeType();
        $size = $file->getSize();
        $checksum = hash_file('sha256', $file->getRealPath());

        $fileName = Str::uuid().'.'.$extension;
        $path = $this->pathGenerator->getPath($model, $fileName);

        // Store file
        Storage::disk($disk)->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        $url = Storage::disk($disk)->url($path);
        $type = $this->mediaTypeResolver->resolve($mime, $extension);

        $media = new Media([
            'disk' => $disk,
            'path' => $path,
            'url' => $url,
            'mime' => $mime,
            'size' => $size,
            'original_name' => $originalName,
            'extension' => $extension,
            'checksum' => $checksum,
            'type' => $type,
            'meta' => null,
            'optimized' => false,
        ]);

        $media->mediable()->associate($model);

        // Dispatch Creating Event
        event(new MediaCreating($media));

        $this->mediaRepository->save($media);

        // Dispatch Created Event
        event(new MediaCreated($media));

        return $media;
    }

    /**
     * Create a Media record from a file that is already stored on the disk (e.g. by Filament).
     */
    public function createFromStoredPath(Model $model, string $path, string $disk = 'r2', ?string $originalName = null): Media
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($path)) {
            throw new \InvalidArgumentException("File does not exist at path: {$path} on disk: {$disk}");
        }

        $size = $storage->size($path);
        $mime = $storage->mimeType($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $originalName = $originalName ?? basename($path);

        // Compute SHA256 checksum safely using streams for files to prevent memory issues
        $checksum = null;
        if ($size < 10 * 1024 * 1024) { // 10MB limit for in-memory hashing
            $content = $storage->get($path);
            $checksum = hash('sha256', $content);
        } else {
            $stream = $storage->readStream($path);
            if ($stream) {
                $context = hash_init('sha256');
                while (! feof($stream)) {
                    hash_update($context, fread($stream, 8192));
                }
                $checksum = hash_final($context);
                fclose($stream);
            }
        }

        // Organize file path: {table_name}/{model_uuid}/{filename}
        $targetPath = $this->pathGenerator->getPath($model, basename($path));

        if ($path !== $targetPath) {
            $storage->move($path, $targetPath);
            $path = $targetPath;
        }

        $url = $storage->url($path);
        $type = $this->mediaTypeResolver->resolve($mime, $extension);

        $media = new Media([
            'disk' => $disk,
            'path' => $path,
            'url' => $url,
            'mime' => $mime,
            'size' => $size,
            'original_name' => $originalName,
            'extension' => $extension,
            'checksum' => $checksum,
            'type' => $type,
            'meta' => null,
            'optimized' => false,
        ]);

        $media->mediable()->associate($model);

        // Dispatch Creating Event
        event(new MediaCreating($media));

        $this->mediaRepository->save($media);

        // Dispatch Created Event
        event(new MediaCreated($media));

        return $media;
    }
}
