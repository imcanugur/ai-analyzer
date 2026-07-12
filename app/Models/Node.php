<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class Node
 *
 * @property string $id
 * @property string $name
 * @property string $driver
 * @property string $endpoint
 * @property string|null $api_key
 * @property string $status
 * @property array|null $capabilities
 * @property int $weight
 * @property int $priority
 * @property int $active_connections
 * @property Carbon|null $last_health_check_at
 * @property string|null $last_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Node extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'driver',
        'endpoint',
        'api_key',
        'status',
        'capabilities',
        'weight',
        'priority',
        'active_connections',
        'last_health_check_at',
        'last_error',
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
            'capabilities' => 'array',
            'weight' => 'integer',
            'priority' => 'integer',
            'active_connections' => 'integer',
            'last_health_check_at' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saving(function (Node $node) {
            if ($node->driver === 'ollama' && ! empty($node->endpoint)) {
                try {
                    $endpoint = rtrim($node->endpoint, '/');
                    $response = Http::timeout(5)->get("{$endpoint}/api/tags");
                    if ($response->successful()) {
                        $data = $response->json();
                        $models = [];
                        if (isset($data['models']) && is_array($data['models'])) {
                            foreach ($data['models'] as $modelData) {
                                if (isset($modelData['name'])) {
                                    $models[] = $modelData['name'];
                                }
                            }
                        }
                        if (! empty($models)) {
                            $node->capabilities = $models;
                            $node->status = 'online';
                            $node->last_health_check_at = now();
                            $node->last_error = null;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("[Node Model] Auto capability fetch failed for {$node->endpoint}: ".$e->getMessage());
                }
            }
        });
    }
}
