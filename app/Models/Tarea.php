<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Task Model
 * 
 * Represents a task in the Kanban board system.
 * Each task belongs to a user and can have multiple audit trail entries.
 * 
 * @property int $id
 * @property string $titulo
 * @property string|null $descripcion
 * @property string $estado
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Tarea extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
    ];

    /**
     * Valid task statuses.
     */
    public const STATUS_PENDING = 'pendiente';
    public const STATUS_IN_PROGRESS = 'en_progreso';
    public const STATUS_COMPLETED = 'completada';

    /**
     * Get all valid task statuses.
     *
     * @return array<int, string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Get the user that owns the task.
     *
     * @return BelongsTo<User, Tarea>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audit trails for the task.
     *
     * @return HasMany<AuditTrail>
     */
    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class);
    }
}
