<?php

namespace App\Contracts;
use App\Models\Company;

interface CompanyRepositoryInterface
{
    public function create(array $attributes):Company;
    public function update(Company $company, string $name): bool;
    public function delete(Company $company): bool;
}
