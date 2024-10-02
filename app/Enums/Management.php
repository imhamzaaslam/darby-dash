<?php

namespace App\Enums;

enum Management: string
{
    // Management Types
    case PROJECT = 'project';
    case USER = 'user';
    case MEMBER = 'member';
    case BUCKS = 'bucks';
    case TASK = 'task';
    case MILESTONE = 'milestone';
    case CHAT = 'chat';
    case FILE = 'file';
    case CALENDAR = 'calendar';

    // Notification Settings
    public static function getNotificationSettings(): array
    {
        return [
            self::PROJECT->value => [
                [
                    'key' => 'project-created',
                    'label' => 'Create New Project',
                ],
                [
                    'key' => 'project-completed',
                    'label' => 'Project Completion',
                ],
                [
                    'key' => 'project-deleted',
                    'label' => 'Project Deletion',
                ],
            ],
            self::USER->value => [
                [
                    'key' => 'user-created',
                    'label' => 'Create New User',
                ],
            ],
            self::MEMBER->value => [
                [
                    'key' => 'memeber-created',
                    'label' => 'Add New Team Member',
                ],
                [
                    'key' => 'memeber-deleted',
                    'label' => 'Team Member Deleted',
                ],
            ],
            self::BUCKS->value => [
                [
                    'key' => 'bucks-award',
                    'label' => 'Bucks Award',
                ],
                [
                    'key' => 'bucks-approved',
                    'label' => 'Bucks Approved',
                ],
                [
                    'key' => 'bucks-rejected',
                    'label' => 'Bucks Rejected',
                ],
            ],
            self::TASK->value => [
                [
                    'key' => 'task-assigned',
                    'label' => 'Task Assignment',
                ],
                [
                    'key' => 'task-unassigned',
                    'label' => 'Task Unassigned',
                ],
                [
                    'key' => 'task-completed',
                    'label' => 'Task Completion',
                ],
            ],
            self::MILESTONE->value => [
                [
                    'key' => 'milestone-completed',
                    'label' => 'Milestone Completion',
                ],
            ],
            self::CHAT->value => [
                [
                    'key' => 'new-message',
                    'label' => 'New Message',
                ],
                [
                    'key' => 'message-replied',
                    'label' => 'Message Replied',
                ],
            ],
            self::FILE->value => [
                [
                    'key' => 'file-uploaded',
                    'label' => 'File Uploaded',
                ],
                [
                    'key' => 'file-shared',
                    'label' => 'File Shared',
                ],
            ],
            self::CALENDAR->value => [
                [
                    'key' => 'event-reminder',
                    'label' => 'Event Reminder',
                ],
                [
                    'key' => 'new-event',
                    'label' => 'New Event',
                ],
            ],
        ];
    }
}
