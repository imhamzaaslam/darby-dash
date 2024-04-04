<?php

namespace Enums;

use App\Enums\JournalStatus;
use App\Models\Journal;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class JournalStatusTest extends TestCase
{
    /**
     * @test
     * @dataProvider provider
     */
    public function it_allows_status_changes(?JournalStatus $currentStatus, JournalStatus $newStatus, bool $bool)
    {
        $this->assertEquals($bool, JournalStatus::changeIsAllowed($currentStatus, $newStatus));
    }

    public static function provider(): array
    {
        return [
            // null
            [null, JournalStatus::CREATED, true],
            [null, JournalStatus::PENDING, false],
            [null, JournalStatus::APPROVED, false],
            [null, JournalStatus::DISAPPROVED, false],
            [null, JournalStatus::BOOKED, false],
            [null, JournalStatus::FAILED, false],
            // CREATED
            [JournalStatus::CREATED, JournalStatus::CREATED, false],
            [JournalStatus::CREATED, JournalStatus::PENDING, true],
            [JournalStatus::CREATED, JournalStatus::APPROVED, false],
            [JournalStatus::CREATED, JournalStatus::DISAPPROVED, false],
            [JournalStatus::CREATED, JournalStatus::BOOKED, false],
            [JournalStatus::CREATED, JournalStatus::FAILED, false],
            // PENDING
            [JournalStatus::PENDING, JournalStatus::CREATED, false],
            [JournalStatus::PENDING, JournalStatus::PENDING, false],
            [JournalStatus::PENDING, JournalStatus::APPROVED, true],
            [JournalStatus::PENDING, JournalStatus::DISAPPROVED, true],
            [JournalStatus::PENDING, JournalStatus::BOOKED, false],
            [JournalStatus::PENDING, JournalStatus::FAILED, false],
            [JournalStatus::PENDING, JournalStatus::MANUAL, true],
            // APPROVED
            [JournalStatus::APPROVED, JournalStatus::CREATED, false],
            [JournalStatus::APPROVED, JournalStatus::PENDING, false],
            [JournalStatus::APPROVED, JournalStatus::APPROVED, false],
            [JournalStatus::APPROVED, JournalStatus::DISAPPROVED, true],
            [JournalStatus::APPROVED, JournalStatus::BOOKED, true],
            [JournalStatus::APPROVED, JournalStatus::FAILED, true],
            [JournalStatus::APPROVED, JournalStatus::MANUAL, true],
            // DISAPPROVED
            [JournalStatus::DISAPPROVED, JournalStatus::CREATED, false],
            [JournalStatus::DISAPPROVED, JournalStatus::PENDING, true],
            [JournalStatus::DISAPPROVED, JournalStatus::APPROVED, true],
            [JournalStatus::DISAPPROVED, JournalStatus::DISAPPROVED, false],
            [JournalStatus::DISAPPROVED, JournalStatus::BOOKED, false],
            [JournalStatus::DISAPPROVED, JournalStatus::FAILED, false],
            [JournalStatus::DISAPPROVED, JournalStatus::MANUAL, true],
            // BOOKED
            [JournalStatus::BOOKED, JournalStatus::CREATED, false],
            [JournalStatus::BOOKED, JournalStatus::PENDING, false],
            [JournalStatus::BOOKED, JournalStatus::APPROVED, false],
            [JournalStatus::BOOKED, JournalStatus::DISAPPROVED, false],
            [JournalStatus::BOOKED, JournalStatus::BOOKED, false],
            [JournalStatus::BOOKED, JournalStatus::FAILED, false],
            [JournalStatus::BOOKED, JournalStatus::MANUAL, false],
            // FAILED
            [JournalStatus::FAILED, JournalStatus::CREATED, false],
            [JournalStatus::FAILED, JournalStatus::PENDING, false],
            [JournalStatus::FAILED, JournalStatus::DISAPPROVED, false],
            [JournalStatus::FAILED, JournalStatus::BOOKED, true],
            [JournalStatus::FAILED, JournalStatus::FAILED, false],
            [JournalStatus::FAILED, JournalStatus::MANUAL, true],
        ];
    }
}
