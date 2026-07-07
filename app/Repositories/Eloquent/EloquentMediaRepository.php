<?php

namespace App\Repositories\Eloquent;

use App\Contracts\MediaRepositoryInterface;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;

class EloquentMediaRepository implements MediaRepositoryInterface
{
    /**
     * Create a media entry and link it polymorphically to a given model.
     */
    public function createForModel(Model $model, array $attributes): Media
    {
        return $model->media()->create($attributes);
    }

    /**
     * Save/persist a Media instance.
     */
    public function save(Media $media): bool
    {
        return $media->save();
    }

    /**
     * Find a media entry by UUID.
     */
    public function find(string $id): ?Media
    {
        return Media::find($id);
    }
}
