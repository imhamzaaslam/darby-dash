<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;

interface ActivityLogRepositoryInterface
{
    public function get(?string $keyword, ?string $orderBy, ?string $order): Paginator;
}
