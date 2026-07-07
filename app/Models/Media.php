<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Media
 *
 *
 * @property string $id
 * @property string|null $mediable_type
 * @property string|null $mediable_id
 * @property string $disk
 * @property string $path
 * @property string $url
 * @property string|null $mime
 * @property int|null $size
 * @property string|null $original_name
 * @property string|null $extension
 * @property string|null $checksum
 * @property string|null $type
 * @property array|null $meta
 * @property bool $optimized
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Model|\Eloquent $mediable
 */
class Media extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'disk',
        'path',
        'url',
        'mime',
        'size',
        'original_name',
        'extension',
        'checksum',
        'type',
        'meta',
        'optimized',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'optimized' => 'boolean',
            'size' => 'integer',
        ];
    }

    /**
     * Get the parent mediable model.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
