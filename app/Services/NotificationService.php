<?php

namespace App\Services;

use App\Notifications\GenericNotification;
use App\Models\User;
use App\Models\Settings_meta;
use App\Enums\Management;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Send a notification based on management type and message type
     *
     * @param string $managementType
     * @param string $messageType
     * @param int|array $receivers
     * @param array $data
     * @return void
     */
    public function sendNotification($managementType, $messageType, $receivers, array $data): void
    {
        $sender = Auth::user();
        $settings = Settings_meta::where('key', $messageType)->where('user_id', $sender->id)->first() ?? null;
        if($settings && !empty($settings->deliverable_channel) && $settings->deliverable_channel !== 'null' && $settings->deliverable_channel !== null)
        {
            $receivers = $this->getReceivers($receivers);
            $receivers = $receivers instanceof User ? [$receivers] : $receivers;
            /* $admins = $this->getAdmins();
            if ($admins && $admins->isNotEmpty()) {
                $receivers = array_merge($receivers, $admins->all());
            } */
            if ($receivers) {
                $template = $this->getTemplateForManagement($managementType, $messageType, $sender, $data);
                foreach ($receivers as $receiver) {
                    $receiver->notify(new GenericNotification($template, $settings->toArray(), $sender, $receiver));
                }
            }
        }
    }

    /**
    * Get receivers from provided ID(s)
    *
    * @param int|array $receivers
    * @return \Illuminate\Database\Eloquent\Collection|\App\Models\User|null
    */

    private function getReceivers($receivers)
    {
        if (is_array($receivers)) {
            return User::whereIn('id', $receivers)->get();
        } elseif (is_numeric($receivers)) {
            return User::find($receivers);
        }

        return null;
    }

    /**
    * Get Admins
    * @return \Illuminate\Database\Eloquent\Collection|\App\Models\User|null
    */

    private function getAdmins()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('role_id', 1);
        })->get();
    }

    /**
     * Get notification template based on management type and message type
     *
     * @param string $managementType
     * @param string $messageType
     * @param User $sender
     * @param array $data
     * @return array
     */
    protected function getTemplateForManagement($managementType, $messageType, $sender, array $data)
    {
        switch ($managementType) {
            case Management::PROJECT->value:
                return $this->getProjectNotificationTemplate($messageType, $sender, $data);
            case Management::USER->value:
                return $this->getUserNotificationTemplate($messageType, $sender, $data);
            case Management::MEMBER->value:
                return $this->getMemberNotificationTemplate($messageType, $sender, $data);
            case Management::BUCKS->value:
                return $this->getBucksNotificationTemplate($messageType, $sender, $data);
            case Management::TASK->value:
                return $this->getTaskNotificationTemplate($messageType, $sender, $data);
            case Management::MILESTONE->value:
                return $this->getMilestoneNotificationTemplate($messageType, $sender, $data);
            case Management::CHAT->value:
                return $this->getChatNotificationTemplate($messageType, $sender, $data);
            case Management::FILE->value:
                return $this->getFileNotificationTemplate($messageType, $sender, $data);
            case Management::CALENDAR->value:
                return $this->getCalendarNotificationTemplate($messageType, $sender, $data);
            default:
                return $this->getDefaultNotificationTemplate();
        }
    }

    protected function getProjectNotificationTemplate($messageType, $sender, array $project)
    {
        switch ($messageType) {
            case 'project-created':
                return [
                    'title' => 'New Project Created',
                    'message' => $sender->name_first . ' added you as a team member of project: ' . $project['title'],
                    'type' => 'project-created',
                    'url' => "projects/web-designs/{$project['uuid']}",
                ];
            case 'project-completed':
                return [
                    'title' => 'Project Completed',
                    'message' => $sender->name_first . ' has marked the '. $project['title'] .' project as completed.',
                    'type' => 'project-completed',
                    'url' => "projects/web-designs/{$project['uuid']}",
                ];
            case 'project-deleted':
                return [
                    'title' => 'Project Deleted',
                    'message' => $sender->name_first . ' has deleted the '. $project['title'] .' project.',
                    'type' => 'project-deleted',
                    'url' => "projects/web-designs",
                ];
            default:
                return $this->getDefaultNotificationTemplate();
        }
    }

    protected function getUserNotificationTemplate($messageType, $sender, array $user)
    {
        $user = getUser($user['id']);
        switch ($messageType) {
            case 'user-created':
                return [
                    'title' => 'Welcome to DarbyDash! Your New Account Awaits',
                    'message' => $sender->name_first . ' added you as a '. ucwords($user->getRoleNames()->first() ?? ''),
                    'type' => 'user-created',
                    'url' => "account-setting",
                ];
            default:
                return $this->getDefaultNotificationTemplate();
        }
    }

    protected function getMemberNotificationTemplate($messageType, $sender, array $project)
    {
        switch ($messageType) {
            case 'member-created':
                return [
                    'title' => 'New Team Member',
                    'message' => $sender->name_first . ' has added you as a new team member to the project ' . $project['title'].".",
                    'type' => 'member-created',
                    'url' => "projects/{$project['uuid']}/team",
                ];
            case 'member-deleted':
                return [
                    'title' => 'You Were Removed as a Team Member',
                    'message' => $sender->name . ' has removed you from the project ' . $project['title'] . '.',
                    'type' => 'member-deleted',
                    'url' => "projects/web-designs",
                ];
            default:
                return $this->getDefaultNotificationTemplate();
        }
    }

    protected function getBucksNotificationTemplate($messageType, $sender, array $data)
    {
        switch ($messageType) {
            case 'bucks-award':
                return [
                    'title' => 'Bucks Awarded - $' . $data['amount'],
                    'message' => $sender->name_first . ' has awarded you $' . $data['amount'] . ' bucks for completing the '. $data['title'] .' project.',
                    'type' => 'bucks-award',
                    'url' => "projects/web-designs",
                ];
            case 'bucks-approved':
                return [
                    'title' => $data['title'] . 'Bucks Approved - $' . $data['amount'],
                    'message' => $sender->name_first . ' has been awarded $' . $data['amount'] . ' bucks for the task: ' . $data['task_title'],
                    'type' => 'bucks-approved',
                    'url' => "projects/{$data['project_uuid']}/bucks?tab=manage-bucks",
                ];
            case 'bucks-rejected':
                return [
                    'title' => $data['title'] . ': Bucks Rejected - $' . $data['amount'],
                    'message' => $sender->name_first . ' has rejected to award bucks for the task: ' . $data['task_title'],
                    'type' => 'bucks-rejected',
                    'url' => "projects/{$data['project_uuid']}/bucks?tab=manage-bucks",
                ];
            default:
                return $this->getDefaultNotificationTemplate();
        }
    }

    protected function getTaskNotificationTemplate($messageType, $sender ,array $data)
    {
        switch ($messageType) {
            case 'task-assigned':
                return [
                    'title' => 'New Task Assigned',
                    'message' => $sender->name_first . ' assigned you a new task: ' . $data['name'],
                    'type' => 'task-assigned',
                    'url' => "projects/{$data['project_uuid']}/tasks/add",
                ];
            case 'task-completed':
                return [
                    'title' => 'Task Completed',
                    'message' => $sender->name_first . ' has completed the task: ' . $data['name'],
                    'type' => 'task-completed',
                    'url' => "projects/{$data['project_uuid']}/tasks/add",
                ];
            case 'task-unassigned':
                return [
                    'title' => 'Task Unassigned',
                    'message' => $sender->name_first . ' unassigned you from task: ' . $data['name'],
                    'type' => 'task-unassigned',
                    'url' => "projects/{$data['project_uuid']}/tasks/add",
                ];
            default:
                return $this->getDefaultNotificationTemplate();
        }
    }

    protected function getMilestoneNotificationTemplate($messageType, $sender, array $data)
    {
        switch ($messageType) {
            case 'milestone-completed':
                return [
                    'title' => $data['title'].': Milestone Completed',
                    'message' => 'We have completed the milestone "' . $data['milestone_title'] . '" for the project "' . $data['title'] . '".',
                    'type' => 'milestone-completed',
                    'url' => null,
                ];
        }
    }

    protected function getChatNotificationTemplate($messageType, $sender, array $data)
    {
        switch ($messageType) {
            case 'new-message':
                return [
                    'title' => 'New message in Project ' . $data['title']. '.',
                    'message' => $sender->name_first. ' has sent a new message.',
                    'type' => 'new-message',
                    'url' => "projects/{$data['uuid']}/chat",
                ];
            case 'message-replied':
                return [
                    'title' => 'Message Replied from ' . $sender->name_first,
                    'message' => 'You have a reply: ' . $data['message'],
                    'type' => 'message-replied',
                    'url' => null,
                ];
        }
    }

    protected function getFileNotificationTemplate($messageType, $sender, array $data)
    {
        switch ($messageType) {
            case 'file-uploaded':
                return [
                    'title' => 'File Uploaded: ' . $data['file_name'],
                    'message' => $sender->name_first . ' uploaded a file: ' . $data['file_name'],
                    'type' => 'file-uploaded',
                    'url' => null,
                ];
            case 'file-shared':
                return [
                    'title' => 'File Shared: ' . $data['file_name'],
                    'message' => $sender->name_first . ' shared a file with you: ' . $data['file_name'],
                    'type' => 'file-shared',
                    'url' => null,
                ];
        }
    }

    protected function getCalendarNotificationTemplate($messageType, $sender, array $data)
    {
        switch ($messageType) {
            case 'event-reminder':
                return [
                    'title' => 'Event Reminder: ' . $data['event_title'],
                    'message' => 'The event "' . $data['event_title'] . '" is happening today.',
                    'url' => null,
                    'type' => 'event-reminder',
                ];
            case 'new-event':
                return [
                    'title' => 'New Meeting on Calendar Created in Project: ' . $data['project_title'],
                    'message' => $sender->name_first . ' created a new meeting on calendar: ' . $data['name'],
                    'type' => 'new-event',
                    'url' => "projects/{$data['project_uuid']}/calendar",
                ];
        }
    }

    // Default notification template
    protected function getDefaultNotificationTemplate()
    {
        return [
            'title' => 'Notification',
            'message' => 'You have a new notification.',
            'url' => null,
            'type' => 'default',
        ];
    }
}
