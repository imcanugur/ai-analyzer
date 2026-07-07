<?php

namespace App\Support;

use App\Contracts\PathGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DefaultPathGenerator implements PathGenerator
{
    /**
     * Get the full path where the file should be stored.
     */
    public function getPath(?Model $model, string $fileName, string $directory = 'media'): string
    {
        return $this->getDirectory($model, $directory) . '/' . $fileName;
    }

    /**
     * Get the directory prefix for storing the model's files.
     */
    public function getDirectory(?Model $model, string $directory = 'media'): string
    {
        $prefix = $this->getStoragePrefix();

        if ($model) {
            return $prefix . '/' . $model->getTable() . '/' . $model->getKey();
        }

        return $prefix . '/' . trim($directory, '/');
    }

    /**
     * Storage path prefix: APP_URL slug / APP_ENV (örn: payshare-example-com/production)
     */
    protected function getStoragePrefix(): string
    {
        $urlSlug = $this->getUrlSlug();
        $env = config('app.env', 'production');

        return $urlSlug . '/' . $env;
    }

    /**
     * Get slugified host name from APP_URL configuration.
     */
    protected function getUrlSlug(): string
    {
        $url = config('app.url', '');
        if (empty($url)) {
            return Str::slug(config('app.name', 'laravel'));
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return Str::slug(config('app.name', 'laravel'));
        }

        return Str::slug(str_replace('.', '-', $host));
    }
}
