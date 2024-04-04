<?php

namespace App\Traits;

use App\Helpers\JournalNotificationMessage;
use App\Models\Journal;
use Illuminate\Support\Collection;

trait GeneratesJournalNotificationMessages
{
    public function getNotificationGroup(Collection $journals): array
    {
        $messages = [];

        $journals->each(function (Journal $journal) use (&$messages) {
            $user = $journal->shop->user;
            $uuid = $user->uuid;

            if (empty($messages[$uuid])) {
                $message = (new JournalNotificationMessage())->setUser($user)->setJournalsCount(1);
                $messages[$uuid] = $message;
            } else {
                $messages[$uuid]->increment();
            }
        });

        return $messages;
    }
}
