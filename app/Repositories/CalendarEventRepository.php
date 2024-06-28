<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\CalendarEventRepositoryInterface;
use App\Models\Base;
use App\Models\CalendarEvent;
use App\Models\Project;

class CalendarEventRepository extends AbstractEloquentRepository implements CalendarEventRepositoryInterface
{
    /**
     * @var CalendarEvent
     */
    protected Base $model;

    public function __construct(CalendarEvent $model)
    {
        parent::__construct($model);
    }

    public function create(Project $project, array $attributes): CalendarEvent
    {
        return $this->model->create(array_merge($attributes, ['project_id' => $project->id]));
    }

    public function syncGuests(CalendarEvent $calendarEvent, array $guestsIds): void
    {
        $calendarEvent->guests()->detach();
        $calendarEvent->guests()->sync($guestsIds);
    }

    public function update(CalendarEvent $calendarEvent, array $attributes): bool
    {
        return $calendarEvent->fill($attributes)->save();
    }

    public function delete(CalendarEvent $calendarEvent): bool
    {
        // delete related entities
        $calendarEvent->guests()->detach();
        return $calendarEvent->delete();
    }
}
