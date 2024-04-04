<?php

namespace App\Enums;

use App\Traits\EnhancesEnum;

enum JournalType: string
{
    use EnhancesEnum;
    case MAIN = 'main';
    case ROLLBACK = 'rollback';
}
