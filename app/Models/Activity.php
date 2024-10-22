<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class Activity extends Base implements ActivityContract
{
    public $guarded = [];

    protected $casts = [
        'properties' => 'collection',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('activitylog.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('activitylog.table_name'));
        }

        parent::__construct($attributes);
    }

    public function subject(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getExtraProperty(string $propertyName, mixed $defaultValue = null): mixed
    {
        return Arr::get($this->properties->toArray(), $propertyName, $defaultValue);
    }

    public function changes(): Collection
    {
        if (! $this->properties instanceof Collection) {
            return new Collection();
        }

        return $this->properties->only(['attributes', 'old']);
    }

    public function getChangesAttribute(): Collection
    {
        return $this->changes();
    }

    public function scopeInLog(Builder $query, ...$logNames): Builder
    {
        if (is_array($logNames[0])) {
            $logNames = $logNames[0];
        }

        return $query->whereIn('log_name', $logNames);
    }

    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        return $query
            ->where('causer_type', $causer->getMorphClass())
            ->where('causer_id', $causer->getKey());
    }

    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }

    public function scopeForEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    public function scopeHasBatch(Builder $query): Builder
    {
        return $query->whereNotNull('batch_uuid');
    }

    public function scopeForBatch(Builder $query, string $batchUuid): Builder
    {
        return $query->where('batch_uuid', $batchUuid);
    }

    public function scopeFiltered(Builder $query, ?string $keyword): Builder
    {
        $activityLogsTable = $this->getTable();

        return $query->when($keyword, function (Builder $query) use ($keyword, $activityLogsTable) {
            return $query->where("$activityLogsTable.causer_id", 'like', '%' . $keyword . '%')
                ->orWhere("$activityLogsTable.subject_id", 'like', '%' . $keyword . '%')
                ->orWhere("$activityLogsTable.subject_type", 'like', '%' . $keyword . '%')
                ->orWhere("$activityLogsTable.log_name", 'like', '%' . $keyword . '%')
                ->orWhere("$activityLogsTable.causer_type", 'like', '%' . $keyword . '%')
                ->orWhere("$activityLogsTable.created_at", 'like', '%' . $keyword . '%')
                ->orWhere("$activityLogsTable.description", 'like', '%' . $keyword . '%');
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'desc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
