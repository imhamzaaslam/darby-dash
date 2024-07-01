<?php

namespace App\Contracts;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    public function create(Project $project, array $data): Payment;
    
    public function update(array $data, Payment $payment): bool;
    
    public function delete(Payment $payment): bool;
}