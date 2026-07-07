<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface PathGenerator
{
    /**
     * Get the full path where the file should be stored.
     */
    public function getPath(?Model $model, string $fileName, string $directory = 'media'): string;

    /**
     * Get the directory prefix for storing the model's files.
     */
    public function getDirectory(?Model $model, string $directory = 'media'): string;
}
