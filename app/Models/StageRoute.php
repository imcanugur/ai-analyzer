<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StageRoute
 *
 * @property string $id
 * @property string $stage
 * @property string $model
 * @property string|null $node_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Node|null $node
 */
class StageRoute extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stage_routes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stage',
        'model',
        'node_id',
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
     * Get the node assigned to this stage route.
     */
    public function node(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }
}
