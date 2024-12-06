<?php

namespace App\Services;

use App\Models\Project;
use App\Models\MileStone;
use App\Models\ProjectList;
use Carbon\Carbon;
use App\Services\ActivityService;
use App\Enums\Management;
use App\Enums\ActionType;
use Illuminate\Support\Collection;

class ProjectProgressService
{

    public function __construct()
    {
        $this->activityService = new ActivityService();
    }

    public function getProgress(Project $project): array
    {
        $lists = $project->lists->map(function ($list) {
            $totalTasks = $list->tasks->count();
            $completedTasks = $list->tasks->where('status', 3)->count();
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $allTasks = $list->tasks;

            return [
                'name' => $list->name,
                'totalTasks' => $totalTasks,
                'progress' => $progress,
                'status' => $this->getTaskBaseStatus($allTasks),
            ];
        });

        $completedLists = $lists->filter(function ($list) {
            return $list['status'] == 'completed';
        });
        $otherLists = $lists->filter(function ($list) {
            return $list['status'] != 'completed';
        });
        $sortedList = $otherLists->merge($completedLists);


        return [
            'lists' => $sortedList,
            'launchingTime' => $this->getLaunchingTime($project),
            'launchingDate' => $this->getLaunchingDate($project),
            'launchingDays' => $this->getLaunchingDays($project),
            'overallProgress' => $this->getOverallProgress($project),
            'totalTasks' => $project->tasks->whereNull('parent_id')->count(),
        ];
    }

    public function getUpcomingTasks(Project $project): Collection
    {
        $upcomingTasks = $project->upcomingTasks()->get();

        return $upcomingTasks;
    }

    public function getProjectListProgress(ProjectList $list): int
    {
        $totalTasks = $list->tasks->count();
        $completedTasks = $list->tasks->where('status', 3)->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return $progress;
    }

    public function getMileStoneProgress($uuid): array
    {
        $mileStone = MileStone::where('uuid', $uuid)->first();
        $project = Project::findOrFail($mileStone->project_id);
        $totalTasks = $mileStone->lists->sum(function ($list) {
            return $list->tasks->count();
        });
        $completedTasks = $mileStone->lists->sum(function ($list) {
            return $list->tasks->where('status', 3)->count();
        });
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $allTasks = $mileStone->lists->map(function ($list) {
            return $list->tasks;
        })->flatten();
        
        if($this->getTaskBaseStatus($allTasks) == 'completed')
        {
            //Send notification & create activity
            $this->activityService->logActivity(Management::MILESTONE, ActionType::COMPLETED, $mileStone->id, $mileStone->toArray(), $project->uuid);
        }

        return [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'progress' => $progress,
            'status' => $this->getTaskBaseStatus($allTasks),
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

    private function getTaskBaseStatus($tasks): string
    {
        $totalTasks = $tasks->count();

        if ($totalTasks == 0) {
            return 'pending';
        }

        $completedTasks = $tasks->where('status', 3)->count();
        $inprogressTasks = $tasks->where('status', 2)->count();
        $pendingTasks = $tasks->where('status', 1)->count();

        if ($totalTasks == $completedTasks) {
            return 'completed';
        }

        if ($totalTasks == $pendingTasks) {
            return 'pending';
        }

        return 'inprogress';
    }

    public function getLaunchingDays(Project $project): int
    {
        $estTimes = $project->uncompletedTasks()->pluck('est_time');
        $totalMinutes = $estTimes->sum();
        $launchingDays = convertMinutesToDays($totalMinutes);
        return $launchingDays;
    }

    public function getLaunchingTime(Project $project): string
    {
        $estTimes = $project->uncompletedTasks()->pluck('est_time');
        $totalMinutes = $estTimes->sum();
        return formatTimeInDaysHoursMinutes($totalMinutes);
    }

    public function getLaunchingDate(Project $project): string
    {
        $tasks = $project->uncompletedTasks()->count();
        $launchingDays = $this->getLaunchingDays($project);
        if (!$launchingDays) {
            return 'Today';
        }

        $currentDate = Carbon::now();
        $launchingDate = $currentDate->addDays($launchingDays);

        return $launchingDate->format('l F j, Y');
    }

    public function getOverallProgress(Project $project): float
    {
        $totalTasks = $project->tasks->whereNull('parent_id')->count();
        $completedTasks = $project->tasks->where('status', 3)->whereNull('parent_id')->count();
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }
}
