<?php

namespace App\Support;

use App\Contracts\PathGenerator;
use Illuminate\Database\Eloquent\Model;

class DefaultPathGenerator implements PathGenerator
{
    /**
     * Get the full path where the file should be stored.
     */
    public function getPath(Model $model, string $fileName): string
    {
        return $this->getDirectory($model) . '/' . $fileName;
    }

    /**
     * Get the directory prefix for storing the model's files.
     */
    public function getDirectory(Model $model): string
    {
        return $model->getTable() . '/' . $model->getKey();
    }
}
