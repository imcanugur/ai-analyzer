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
    protected string $disk;

    public function __construct(
        protected PathGenerator $pathGenerator,
        protected MediaTypeResolver $mediaTypeResolver,
        protected MediaRepositoryInterface $mediaRepository,
        ?string $disk = null
    ) {
        $this->disk = $disk ?? config('filesystems.default', 'r2');
    }

    /**
     * Swap the active target storage disk at runtime.
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Store a file (UploadedFile or pre-stored path) and attach it to a given model.
     * Keeps compatibility with existing actions and Filament.
     */
    public function createMedia(Model $model, UploadedFile|string $file, string $disk = 'r2', ?string $sourceDisk = null, ?string $originalName = null): Media
    {
        $this->setDisk($disk);

        if ($file instanceof UploadedFile) {
            return $this->save($file, $model);
        }

        return $this->createFromStoredPath($model, $file, $disk, $sourceDisk, $originalName);
    }

    /**
     * Save an uploaded file, handling optional optimization.
     */
    public function save(
        UploadedFile $file,
        ?Model $mediable = null,
        string $directory = 'media',
        ?string $type = null,
        bool $optimize = false
    ): Media {
        if (!$file->isValid()) {
            throw new \RuntimeException('Invalid file upload attempt.');
        }

        $contents = $optimize
            ? $this->optimize($file)
            : file_get_contents($file->getRealPath());

        $mime = $file->getClientMimeType() ?: $file->getMimeType();
        $filename = $file->getClientOriginalName();

        return $this->saveRawContent(
            $contents,
            $filename,
            $mime,
            $mediable,
            $directory,
            $type
        );
    }

    /**
     * Save raw file contents directly to storage and create a Media entry.
     */
    public function saveRawContent(
        string $contents,
        string $filename,
        string $mime,
        ?Model $mediable = null,
        string $directory = 'media',
        mixed $type = null,
        ?string $forcePath = null
    ): Media {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($forcePath) {
            $path = $this->pathGenerator->getDirectory($mediable, $directory) . '/' . ltrim($forcePath, '/');
        } else {
            $unique = uniqid('', true) . '_' . Str::random(6) . ($extension ? '.' . $extension : '');
            $path = $this->pathGenerator->getPath($mediable, $unique, $directory);
        }

        Storage::disk($this->disk)->put($path, $contents);

        $url = $this->getUrl($path);
        
        $checksum = hash('sha256', $contents);

        $resolvedType = $type instanceof \App\Enums\MediaType 
            ? $type 
            : ($type ? \App\Enums\MediaType::tryFrom($type) : $this->mediaTypeResolver->resolve($mime, $extension));

        $media = new Media([
            'disk' => $this->disk,
            'path' => $path,
            'url' => $url,
            'mime' => $mime,
            'size' => strlen($contents),
            'original_name' => $filename,
            'extension' => $extension,
            'checksum' => $checksum,
            'type' => $resolvedType,
            'meta' => null,
            'optimized' => false,
        ]);

        if ($mediable) {
            $media->mediable()->associate($mediable);
        }

        // Dispatch Creating Event
        event(new MediaCreating($media));

        $this->mediaRepository->save($media);

        // Dispatch Created Event
        event(new MediaCreated($media));

        return $media;
    }

    /**
     * Get the public URL of a file path, taking CDN URLs into account.
     */
    public function getUrl(string $path): string
    {
        $cdn = config("filesystems.disks.{$this->disk}.cdn_url");

        return $cdn ? rtrim($cdn, '/') . '/' . $path : Storage::disk($this->disk)->url($path);
    }

    /**
     * Generate a unique path for a file using the strategy prefix.
     */
    public function generatePath(string $filename, string $directory = 'media'): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $unique = uniqid('', true) . '_' . Str::random(6) . ($extension ? '.' . $extension : '');

        return $this->pathGenerator->getPath(null, $unique, $directory);
    }

    /**
     * Optimize image, pdf, or audio/video contents if matching packages and system binaries exist.
     */
    protected function optimize(UploadedFile $file): string
    {
        $mime = $file->getMimeType();
        $contents = file_get_contents($file->getRealPath());

        if (str_starts_with($mime, 'image/') && class_exists(\Intervention\Image\ImageManager::class)) {
            try {
                $manager = \Intervention\Image\ImageManager::gd();
                $image = $manager->read($file->getRealPath())
                    ->resize(720)
                    ->toJpeg(75);

                return (string) $image;
            } catch (\Throwable $e) {
                // Degrade gracefully
            }
        }

        if ($mime === 'application/pdf') {
            $tmpIn = $file->getRealPath();
            $tmpOut = sys_get_temp_dir() . '/' . uniqid() . '.pdf';

            if (shell_exec('which gs')) {
                $cmd = 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook ' .
                    "-dNOPAUSE -dQUIET -dBATCH -sOutputFile={$tmpOut} {$tmpIn}";
                exec($cmd);

                if (file_exists($tmpOut)) {
                    $optContents = file_get_contents($tmpOut);
                    unlink($tmpOut);
                    return $optContents;
                }
            }
        }

        if (str_starts_with($mime, 'video/') || str_starts_with($mime, 'audio/')) {
            $tmpIn = $file->getRealPath();
            $tmpOut = sys_get_temp_dir() . '/' . uniqid() . '.' . $file->getClientOriginalExtension();

            if (shell_exec('which ffmpeg')) {
                $cmd = "ffmpeg -i {$tmpIn} -b:v 1000k -b:a 128k -y {$tmpOut}";
                exec($cmd);

                if (file_exists($tmpOut)) {
                    $optContents = file_get_contents($tmpOut);
                    unlink($tmpOut);
                    return $optContents;
                }
            }
        }

        return $contents;
    }

    /**
     * Upload multiple files sequentially.
     */
    public function upload(
        array $files,
        ?Model $mediable = null,
        string $directory = 'media',
        ?string $type = null,
        bool $optimize = false
    ): array {
        $results = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $results[] = $this->save($file, $mediable, $directory, $type, $optimize);
            }
        }

        return $results;
    }

    /**
     * Delete a media item and its physical storage file.
     */
    public function delete(Media $media): bool
    {
        Storage::disk($media->disk)->delete($media->path);

        return $media->delete();
    }

    /**
     * Delete multiple media items.
     */
    public function deleteMultiple(array $mediaItems): int
    {
        $count = 0;
        foreach ($mediaItems as $media) {
            if ($media instanceof Media) {
                Storage::disk($media->disk)->delete($media->path);
                $media->delete();
                $count++;
            }
        }

        return $count;
    }

    /**
     * Query the Media model.
     */
    public function query()
    {
        return Media::query();
    }

    /**
     * Create a Media record from a file that is already stored on the disk (e.g. locally by Filament).
     * Streams cross-disk on the backend if local is different from destination disk.
     */
    public function createFromStoredPath(Model $model, string $path, string $disk = 'r2', ?string $sourceDisk = null, ?string $originalName = null): Media
    {
        $targetStorage = Storage::disk($disk);
        $sourceDisk = $sourceDisk ?? config('filesystems.default', 'local');
        $sourceStorage = Storage::disk($sourceDisk);

        if (!$sourceStorage->exists($path)) {
            throw new \InvalidArgumentException("File does not exist at path: {$path} on source disk: {$sourceDisk}");
        }

        $size = $sourceStorage->size($path);
        $mime = $sourceStorage->mimeType($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $originalName = $originalName ?? basename($path);
        
        // Compute SHA256 checksum safely using streams from the source disk
        $checksum = null;
        $stream = $sourceStorage->readStream($path);
        if ($stream) {
            $context = hash_init('sha256');
            while (!feof($stream)) {
                hash_update($context, fread($stream, 8192));
            }
            $checksum = hash_final($context);
            fclose($stream);
        }

        // Organize file path: {host_slug}/{env}/{table_name}/{model_uuid}/{filename}
        $targetPath = $this->pathGenerator->getPath($model, basename($path));

        // Copy file across disks if source is different from destination
        if ($sourceDisk !== $disk) {
            $readStream = $sourceStorage->readStream($path);
            if ($readStream) {
                $targetStorage->writeStream($targetPath, $readStream);
                fclose($readStream);
            }
            // Delete from source disk after successful copy
            $sourceStorage->delete($path);
        } else {
            // Same disk, just move
            if ($path !== $targetPath) {
                $targetStorage->move($path, $targetPath);
            }
        }

        $path = $targetPath;
        $url = $this->getUrl($path);
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
