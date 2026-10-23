<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, CrudTrait;

    protected $fillable = [
        'user_id',
        'user_name',
        'auditable_type',
        'auditable_id',
        'auditable_name',
        'action',
        'old_values',
        'new_values',
        'description',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function record($action, Model $model, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): self
    {
        $user = auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'auditable_name' => $model->name ?? ($model->title ?? ('#' . $model->getKey())),
            'action' => strtoupper($action),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description ?? sprintf('%s %s "%s"', $user?->name ?? 'System', strtolower($action), $model->name ?? '#' . $model->getKey()),
            'ip_address' => request()?->ip() ?? '127.0.0.1',
        ]);
    }
}
