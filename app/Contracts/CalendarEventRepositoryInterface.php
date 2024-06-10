<?php

namespace App\Contracts;

use App\Models\CalendarEvent;
use App\Models\Project;
use Illuminate\Support\Collection;

interface CalendarEventRepositoryInterface
{
    public function create(Project $project, array $attributes): CalendarEvent;

    public function syncGuests(CalendarEvent $calendarEvent, array $guestsIds): void;

    public function update(CalendarEvent $calendarEvent, array $attributes): bool;

    public function delete(CalendarEvent $calendarEvent): bool;
}
