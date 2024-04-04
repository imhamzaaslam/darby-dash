<?php

namespace App\Enums;

enum InvoiceType: string
{
    case ADVERTISING = 'ADVERTISING_VIA_BOL';
    case ALL_IN_ONE = 'ALL_IN_ONE';
}
