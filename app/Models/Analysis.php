<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Analysis
 *
 * @package App\Models
 *
 * @property string $id
 * @property string $submission_id
 * @property string $type
 * @property string|null $category
 * @property string|null $provider
 * @property string|null $engine
 * @property string|null $model
 * @property string|null $version
 * @property string $status
 * @property array|null $config
 * @property array|null $metadata
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property string|null $error
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read Submission $submission
 * @property-read \Illuminate\Database\Eloquent\Collection|AnalysisResult[] $results
 * @property-read \Illuminate\Database\Eloquent\Collection|Report[] $reports
 */
class Analysis extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'analyses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'submission_id',
        'type',
        'category',
        'provider',
        'engine',
        'model',
        'version',
        'status',
        'config',
        'metadata',
        'started_at',
        'completed_at',
        'error',
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
            'config' => 'array',
            'metadata' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the submission that owns the analysis.
     *
     * @return BelongsTo
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the results for the analysis.
     *
     * @return HasMany
     */
    public function results(): HasMany
    {
        return $this->hasMany(AnalysisResult::class);
    }

    /**
     * Get the reports for the analysis.
     *
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
