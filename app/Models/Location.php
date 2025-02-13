<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'notify_by',
        'coordinates',
    ];

    protected function casts(): array
    {
        return [
            'coordinates' => 'array',
        ];
    }

    protected function notifyBy(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => explode(',', $value),
            set: fn (array $value) => implode(',', array_filter($value)),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include locations with has notification enabled.
     */
    public function scopeHasNotifyBy(Builder $query): void
    {
        $query->where('notify_by', '!=', '');
    }
}
