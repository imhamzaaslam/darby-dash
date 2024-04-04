<?php

namespace App\Enums;

enum Platform: string
{
    case BOL_COM = 'Bol.com';
    case YUKI = 'Yuki';

    public function getStrippedName(): string
    {
        $withoutPunctuation = preg_replace('#[[:punct:]]#', '', $this->value);
        return strtolower($withoutPunctuation);
    }
}
