<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use App\Enums\Management;
use App\Enums\ActionType;

class ActivityService
{
    public function logActivity(Management $entityType, ActionType $action, $entityId, $properties = [], $projectUuid = null)
    {
        $logName = $this->getLogNameByEntity($entityType);
        $result = $this->getDescriptionByAction($entityType, $action, $entityId, $properties);

        $properties['log_title'] = $result['title'];
        $properties['log_subtitle'] = $result['subtitle'];

        $activity = activity($logName)  
            ->performedOn($this->getEntityInstance($entityType, $entityId))
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($properties['log_title']);

        if ($projectUuid) {
            $activity->batch_uuid = $projectUuid;
            $activity->save();
        }
    }

    protected function getLogNameByEntity(Management $entityType): string
    {
        return $entityType->value;
    }

    protected function getDescriptionByAction(Management $entityType, ActionType $action, $entityId, $properties = [])
    {
        $entity = $this->getEntityInstance($entityType, $entityId);
        $entityName = $this->getEntityName($entityType, $entityId);
        $userName = auth()->user()->name_first.' '.auth()->user()->name_last ?? 'Someone';
        return match($entityType) {
            Management::PROJECT => $this->getProjectMessage($action, $entity, $entityName, $userName, $properties),
            Management::USER => $this->getUserMessage($action, $entity, $entityName, $userName, $properties),
            Management::MEMBER => $this->getMemberMessage($action, $entity, $entityName, $userName, $properties),
            Management::BUCKS => $this->getBucksMessage($action, $entity, $entityName, $userName, $properties),
            Management::TASK => $this->getTaskMessage($action, $entity, $entityName, $userName, $properties),
            Management::MILESTONE => $this->getMilestoneMessage($action, $entity, $entityName, $userName, $properties),
            Management::CHAT => $this->getChatMessage($action, $entity, $entityName, $userName, $properties),
            Management::FILE => $this->getFileMessage($action, $entity, $entityName, $userName, $properties),
            Management::CALENDAR => $this->getCalendarMessage($action, $entity, $entityName, $userName, $properties),
            default => "$entityName activity logged by $userName.",
        };
    }

    protected function getProjectMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "Project '$entityName' created.",
                'subtitle' => "$userName created the project '$entityName'."
            ],
            ActionType::COMPLETED => [
                'title' => "Project '$entityName' completed.",
                'subtitle' => "$userName marked the project '$entityName' as completed."
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getUserMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "User '$entityName' added.",
                'subtitle' => "$userName added a new user '$entityName'."
            ],
            ActionType::UPDATED => [
                'title' => "User '$entityName' updated.",
                'subtitle' => "$userName updated the user '$entityName'."
            ],
            ActionType::DELETED => [
                'title' => "User '$entityName' deleted.",
                'subtitle' => "$userName removed the user '$entityName'."
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getMemberMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "Team member '$entityName' added.",
                'subtitle' => "$userName added a new team member '$entityName'."
            ],
            ActionType::UPDATED => [
                'title' => "Team member '$entityName' updated.",
                'subtitle' => "$userName updated the team member '$entityName'."
            ],
            ActionType::DELETED => [
                'title' => "Team member '$entityName' removed.",
                'subtitle' => "$userName removed the team member '$entityName'."
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getBucksMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::AWARDED => [
                'title' => 'Bucks Awarded - $' . $data['amount'],
                'subtitle' => $userName . ' has awarded you $' . $data['amount'] . ' bucks for completing the '. $data['title'] .' project.',
            ],
            ActionType::APPROVED => [
                'title' => $data['title'] . 'Bucks Approved - $' . $data['amount'],
                'subtitle' => $userName . ' has been awarded $' . $data['amount'] . ' bucks for the task: ' . $data['task_title'],
            ],
            ActionType::REJECTED => [
                'title' => $data['title'] . ': Bucks Rejected - $' . $data['amount'],
                'subtitle' => $userName . ' has rejected to award bucks for the task: ' . $data['task_title'],
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getTaskMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "New task created.",
                'subtitle' => "$userName created a new task '$entityName'."
            ],
            ActionType::UPDATED => [
                'title' => "Task updated.",
                'subtitle' => "$userName updated the task '$entityName'."
            ],
            ActionType::COMPLETED => [
                'title' => "Task completed.",
                'subtitle' => "$userName completed the task '$entityName'."
            ],
            ActionType::ASSIGNED => [
                'title' => "Task successfully assigned to ".$data['assignee_name'].".",
                'subtitle' => "$userName has assigned the task '$entityName'."
            ],
            ActionType::UNASSIGNED => [
                'title' => "Task successfully un-assigned from ".$data['assignee_name'].".",
                'subtitle' => "$userName has un-assigned the task '$entityName'."
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getMilestoneMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "New milestone '$entityName' created.",
                'subtitle' => "$userName created a new milestone '$entityName'."
            ],
            ActionType::UPDATED => [
                'title' => "Milestone '$entityName' updated.",
                'subtitle' => "$userName updated the milestone '$entityName'."
            ],
            ActionType::DELETED => [
                'title' => "Milestone '$entityName' deleted.",
                'subtitle' => "$userName deleted the milestone '$entityName'."
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getChatMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "New chat '{$entityName}' started.",
                'subtitle' => "$userName has started a new chat titled '{$entityName}'."
            ],
            ActionType::UPDATED => [
                'title' => "Chat '{$entityName}' updated.",
                'subtitle' => "$userName has updated the chat titled '{$entityName}'."
            ],
            ActionType::DELETED => [
                'title' => "Chat '{$entityName}' deleted.",
                'subtitle' => "$userName has deleted the chat titled '{$entityName}'."
            ],
            ActionType::VIEWED => [
                'title' => "Chat '{$entityName}' viewed.",
                'subtitle' => "$userName has viewed the chat titled '{$entityName}'."
            ],
            ActionType::REPLIED => [
                'title' => "Reply sent in chat '{$entityName}'.",
                'subtitle' => "$userName has replied in the chat titled '{$entityName}'."
            ],
            ActionType::FORWARDED => [
                'title' => "Chat '{$entityName}' forwarded.",
                'subtitle' => "$userName has forwarded the chat titled '{$entityName}'."
            ],
            ActionType::ARCHIVED => [
                'title' => "Chat '{$entityName}' archived.",
                'subtitle' => "$userName has archived the chat titled '{$entityName}'."
            ],
            ActionType::UNARCHIVED => [
                'title' => "Chat '{$entityName}' unarchived.",
                'subtitle' => "$userName has unarchived the chat titled '{$entityName}'."
            ],
            ActionType::PINNED => [
                'title' => "Chat '{$entityName}' pinned.",
                'subtitle' => "$userName has pinned the chat titled '{$entityName}' for easy access."
            ],
            ActionType::UNPINNED => [
                'title' => "Chat '{$entityName}' unpinned.",
                'subtitle' => "$userName has unpinned the chat titled '{$entityName}'."
            ],
            default => [
                'title' => "Chat activity logged.",
                'subtitle' => "No specific details available."
            ],
        };        
    }

    protected function getFileMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::UPLOADED => [
                'title' => "File '{$data['name']}' uploaded.",
                'subtitle' => "$userName uploaded the file '{$data['name']}'."
            ],
            ActionType::UPDATED => [
                'title' => "File '{$data['name']}' updated.",
                'subtitle' => "$userName updated the file '{$data['name']}'."
            ],
            ActionType::DELETED => [
                'title' => "File '{$data['name']}' deleted.",
                'subtitle' => "$userName deleted the file '{$data['name']}'."
            ],
            default => [
                'title' => "{$data['name']} activity logged.",
                'subtitle' => "$userName logged an activity for the file '{$data['name']}'."
            ],
        };        
    }

    protected function getCalendarMessage(ActionType $action, $entity, string $entityName, string $userName, $data)
    {
        return match($action) {
            ActionType::CREATED => [
                'title' => "New event '{$entityName}' created.",
                'subtitle' => "$userName created a new event '{$entityName}'."
            ],
            ActionType::UPDATED => [
                'title' => "Event '{$entityName}' updated.",
                'subtitle' => "$userName updated the event '{$entityName}'."
            ],
            ActionType::DELETED => [
                'title' => "Event '{$entityName}' deleted.",
                'subtitle' => "$userName deleted the event '{$entityName}'."
            ],
            default => "$entityName activity logged.",
        };
    }

    protected function getEntityInstance(Management $entityType, $entityId)
    {
        return match($entityType) {
            Management::PROJECT => \App\Models\Project::find($entityId),
            Management::USER => \App\Models\User::find($entityId),
            Management::MEMBER => \App\Models\ProjectMember::find($entityId),
            Management::BUCKS => \App\Models\ProjectBucks::find($entityId),
            Management::TASK => \App\Models\Task::find($entityId),
            Management::MILESTONE => \App\Models\Milestone::find($entityId),
            Management::CHAT => \App\Models\Chat::find($entityId),
            Management::FILE => \App\Models\File::find($entityId),
            Management::CALENDAR => \App\Models\CalendarEvent::find($entityId),
            default => null,
        };
    }

    protected function getEntityName(Management $entityType, $entityId): string
    {
        $entity = $this->getEntityInstance($entityType, $entityId);

        if ($entityType === Management::MEMBER && $entity) {
            return "{$entity->user->name_first} {$entity->user->name_last}";
        }

        return $entity ? ($entity->name ?? $entity->title ?? 'Unknown') : 'Unknown';
    }
}
