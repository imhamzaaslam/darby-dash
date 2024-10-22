<?php

namespace App\Enums;

enum ActionType: string
{
    // Action Types
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case VIEWED = 'viewed';
    case COMPLETED = 'completed';
    case ASSIGNED = 'assigned';
    case UNASSIGNED = 'unassigned';
    case AWARDED = 'awarded';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REPLIED = 'replied';
    case FORWARDED = 'forwarded';
    case ARCHIVED = 'archived';
    case UNARCHIVED = 'unarchived';
    case PINNED = 'pinned';
    case UNPINNED = 'unpinned';
    case UPLOADED = 'uploaded';
}
