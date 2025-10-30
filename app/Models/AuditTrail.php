<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * AuditTrail Model
 * 
 * Tracks all changes made to tasks and other models in the system.
 * Each audit trail entry records who made the change, what action was performed,
 * and the before/after state of the data.
 * 
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string $model_type
 * @property int $model_id
 * @property array|null $old_data
 * @property array|null $new_data
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class AuditTrail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
        'model_id' => 'integer',
    ];

    /**
     * Valid audit actions.
     */
    public const ACTION_CREATED = 'created';
    public const ACTION_UPDATED = 'updated';
    public const ACTION_DELETED = 'deleted';
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';

    /**
     * Get the user who performed the action.
     *
     * @return BelongsTo<User, AuditTrail>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the owning model.
     *
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the formatted action name.
     *
     * @return string
     */
    public function getFormattedActionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Get the model name without namespace.
     *
     * @return string
     */
    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }
}
