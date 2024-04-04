<?php

namespace App\Listeners;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Events\PasswordReset;

class ActivateUser
{
    protected UserRepositoryInterface $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     *
     * @param  PasswordReset  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        $this->repository->activate($event->user);
    }
}
