<?php

namespace App\Services;

use App\Models\Project;
use App\Models\MileStone;

class ProjectProgressService
{
    public function getProgress(Project $project): array
    {
        $lists = $project->lists->map(function ($list) {
            $totalTasks = $list->tasks->count();
            $completedTasks = $list->tasks->where('status', 3)->count();
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            return [
                'name' => $list->name,
                'totalTasks' => $totalTasks,
                'progress' => $progress,
                'status' => $this->getStatus($progress),
            ];
        });

        return [
            'lists' => $lists,
            'launchingDays' => $this->getLaunchingDays($project),
            'launchingDate' => $this->getLaunchingDate($project),
            'overallProgress' => $this->getOverallProgress($project),
            'totalTasks' => $project->tasks->whereNull('parent_id')->count(),
        ];
    }

    public function getMileStoneProgress($uuid): array
    {
        $mileStone = MileStone::where('uuid', $uuid)->first();
        $totalTasks = $mileStone->lists->sum(function ($list) {
            return $list->tasks->count();
        });
        $completedTasks = $mileStone->lists->sum(function ($list) {
            return $list->tasks->where('status', 3)->count();
        });
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'progress' => $progress,
            'status' => $this->getStatus($progress),
        ];
    }

    private function getStatus(float $progress): string
    {
        if ($progress == 100) {
            return 'completed';
        }
        if ($progress == 0) {
            return 'pending';
        }
        return 'inprogress';
    }

    public function getLaunchingDays(Project $project): int
    {
        $lastTask = $project->tasks()->whereNotNull('due_date')->orderBy('due_date', 'desc')->first();
        if (!$lastTask) {
            return 0;
        }

        return now()->diffInDays($lastTask->due_date, false);
    }

    public function getLaunchingDate(Project $project): string
    {
        $lastTask = $project->tasks()->whereNotNull('due_date')->orderBy('due_date', 'desc')->first();
        if (!$lastTask) {
            return 'No due date';
        }

        if ($lastTask->due_date->isToday()) {
            return $lastTask->due_date->format('l F j, Y');
        }

        return $lastTask->due_date->format('l F j, Y');
    }

    public function getOverallProgress(Project $project): float
    {
        $totalTasks = $project->tasks->whereNull('parent_id')->count();
        $completedTasks = $project->tasks->where('status', 3)->whereNull('parent_id')->count();
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }
}