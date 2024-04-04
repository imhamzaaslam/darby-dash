<?php

namespace App\Enums;

use App\Traits\EnhancesEnum;

enum JournalStatus: string
{
    use EnhancesEnum;

    case APPROVED = 'approved';
    case BOOKED = 'booked';
    case CREATED = 'created';
    case DISAPPROVED = 'disapproved';
    case FAILED = 'failed';
    case PENDING = 'pending';
    case MANUAL = 'manual';

    public static function changeIsAllowed(?self $currentStatus, self $newStatus): bool
    {
        /*
         * This method is responsible for checking whether the status change is allowed.
         */
        return match ($currentStatus) {
            /*
             * No status (null)
             * -> Created
             */
            null => $newStatus === self::CREATED,
            /*
             * Created
             * -> Pending
             */
            self::CREATED => $newStatus === self::PENDING,
            /*
             * Pending
             * -> Approved
             * -> Disapproved
             */
            self::PENDING => in_array($newStatus, [self::APPROVED, self::DISAPPROVED, self::MANUAL], true),
            /*
             * Approved
             * -> Disapproved
             * -> Booked
             */
            self::APPROVED => in_array($newStatus, [self::DISAPPROVED, self::BOOKED, self::FAILED, self::MANUAL], true),
            /*
             * Disapproved
             * -> Pending
             * -> Approved
             */
            self::DISAPPROVED => in_array($newStatus, [self::PENDING, self::APPROVED, self::MANUAL], true),
            /**
             * Failed
             * -> Manual
             */
            self::FAILED => in_array($newStatus, [self::MANUAL, self::BOOKED], true),
            /*
             * FINAL STATUSES:
             * -> Booked
             * -> Manual
             * -> Unknown status always returns false
             */
            default => false,
        };
    }

    public static function implode(): string
    {
        return implode(',', self::values());
    }

    public static function toArray(): array
    {
        return self::values();
    }
}
