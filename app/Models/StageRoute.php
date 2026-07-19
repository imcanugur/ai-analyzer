<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StageRoute
 *
 * @property string $id
 * @property string $stage
 * @property string|null $name
 * @property string|null $description
 * @property array|null $dependencies
 * @property string $on_failure
 * @property int $max_retries
 * @property int $timeout_seconds
 * @property float|null $temperature
 * @property int|null $max_tokens
 * @property string $output_format
 * @property array|null $config
 * @property string|null $prompt_template
 * @property string|null $system_prompt
 * @property string $model
 * @property string|null $node_id
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Node|null $node
 */
class StageRoute extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'stage_routes';

    protected $fillable = [
        'stage',
        'name',
        'description',
        'dependencies',
        'on_failure',
        'max_retries',
        'timeout_seconds',
        'temperature',
        'max_tokens',
        'output_format',
        'config',
        'prompt_template',
        'system_prompt',
        'model',
        'node_id',
        'sort_order',
        'is_active',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'dependencies' => 'array',
            'config' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'max_retries' => 'integer',
            'timeout_seconds' => 'integer',
            'temperature' => 'float',
            'max_tokens' => 'integer',
        ];
    }

    /**
     * Get the node assigned to this stage route.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    /**
     * Scope query to only include active stages.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to order stages by sequence.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Check if all required prerequisite dependencies are completed for the given analysis.
     *
     * @param  array<string, string>  $completedStageStatuses  Map of stage key => status
     */
    public function isReadyToExecute(array $completedStageStatuses): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $dependencies = $this->dependencies ?? [];
        if (empty($dependencies)) {
            return true;
        }

        foreach ($dependencies as $prerequisiteStage) {
            $status = $completedStageStatuses[$prerequisiteStage] ?? null;
            // Prerequisite must be either completed or skipped/failed (if allowed)
            if ($status !== 'completed' && $status !== 'failed') {
                return false;
            }
        }

        return true;
    }
}
