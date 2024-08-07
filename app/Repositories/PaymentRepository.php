<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\PaymentRepositoryInterface;
use App\Models\Base;
use App\Models\Payment;
use App\Models\User;
use App\Models\Project;

class PaymentRepository extends AbstractEloquentRepository implements PaymentRepositoryInterface
{
    /**
     * @var Payment
     */
    protected Base $model;

    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function create(Project $project, array $data): Payment
    {
        $data['user_id'] = auth()->id();
        $data['project_id'] = $project->id;
        return $this->model->create($data);
    }

    public function update(array $data, Payment $payment): bool
    {
        return $payment->update($data);
    }

    public function delete(Payment $payment): bool
    {
        return $payment->delete();
    }
}
