<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnalysisResult
 *
 * @package App\Models
 *
 * @property string $id
 * @property string $analysis_id
 * @property string $stage
 * @property string $status
 * @property float|null $score
 * @property array|null $payload
 * @property array|null $metadata
 * @property int|null $execution_time
 * @property int|null $tokens
 * @property float|null $cost
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read Analysis $analysis
 */
class AnalysisResult extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'analysis_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'analysis_id',
        'stage',
        'status',
        'score',
        'payload',
        'metadata',
        'execution_time',
        'tokens',
        'cost',
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
            'score' => 'float',
            'payload' => 'array',
            'metadata' => 'array',
            'execution_time' => 'integer',
            'tokens' => 'integer',
            'cost' => 'float',
        ];
    }

    /**
     * Get the analysis that owns the result.
     *
     * @return BelongsTo
     */
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }
}
