<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Submission
 *
 * @package App\Models
 *
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property array|null $metadata
 * @property \Carbon\Carbon|null $submitted_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|Analysis[] $analyses
 * @property-read \Illuminate\Database\Eloquent\Collection|Media[] $media
 */
class Submission extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'metadata',
        'submitted_at',
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
            'metadata' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the submission.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the analyses for the submission.
     *
     * @return HasMany
     */
    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }

    /**
     * Get all of the submission's media.
     *
     * @return MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
