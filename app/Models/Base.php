<?php

namespace App\Models;

use App\Contracts\BaseInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\Base
 *
 * @method static Builder|Base newModelQuery()
 * @method static Builder|Base newQuery()
 * @method static Builder|Base query()
 * @mixin \Eloquent
 */
class Base extends Model implements BaseInterface
{
    use HasFactory;
    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (Schema::hasColumn($model->getTable(), 'uuid')) {
                //check if model has uuid column then save uuid for it.
                $model->uuid = str()->uuid();
            }
        });
    }

    public function getCreatedAttribute(): string
    {
        return $this->created_at->toISO8601String();
    }

    public function getUpdatedAttribute(): string
    {
        return $this->created_at->toISO8601String();
    }

    public function getISO8601StringFor(?Carbon $date = null): ?string
    {
        return $date?->toIso8601String() ?? null;
    }

    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
