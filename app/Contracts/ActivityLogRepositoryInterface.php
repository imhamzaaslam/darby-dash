<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ActivityLogRepositoryInterface
{
    public function get(string $uuid): Collection;
}
