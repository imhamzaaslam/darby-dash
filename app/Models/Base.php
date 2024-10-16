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
    protected static $excludedFilters = [
        'project_types',
        'project_lists',
        'statuses',
        'template_lists',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check()) {
                $companyId = auth()->user()->company_id;
                if (in_array((new static())->getTable(), self::$excludedFilters)) {
                    return;
                }
                $modelInstance = new static();
                if (Schema::hasColumn($modelInstance->getTable(), 'created_by')) {
                    $builder->whereHas('creator', function ($query) use ($companyId) {
                        $query->where('company_id', $companyId);
                    });
                }
            }
        });

        static::creating(function (self $model) {
            if (Schema::hasColumn($model->getTable(), 'uuid')) {
                //check if model has uuid column then save uuid for it.
                $model->uuid = str()->uuid();
            }

            if (auth()->check() && Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function (self $model) {
            if (auth()->check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function (self $model) {
            if (auth()->check() && Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = auth()->id();
                $model->save();
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
