<?php

namespace App\Contracts;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;

interface MediaRepositoryInterface
{
    /**
     * Create a media entry and link it polymorphically to a given model.
     */
    public function createForModel(Model $model, array $attributes): Media;

    /**
     * Save/persist a Media instance.
     */
    public function save(Media $media): bool;

    /**
     * Find a media entry by UUID.
     */
    public function find(string $id): ?Media;
}
