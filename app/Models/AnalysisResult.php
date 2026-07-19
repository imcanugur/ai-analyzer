<?php

namespace App\Models;

use App\Enums\AnalysisStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnalysisResult
 *
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
        'node_id',
        'model',
        'driver',
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
            'status' => AnalysisStatus::class,
            'score' => 'float',
            'payload' => 'array',
            'metadata' => 'array',
            'execution_time' => 'integer',
            'tokens' => 'integer',
            'cost' => 'float',
        ];
    }

    /**
     * Encode the given value to JSON with unescaped Unicode characters.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value, $flags = 0)
    {
        return json_encode($value, $flags | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get the analysis that owns the result.
     */
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    /**
     * Get the cluster node that processed the result.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}
