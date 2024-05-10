<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface RoleRepositoryInterface
{
    public function get(): Collection;
}