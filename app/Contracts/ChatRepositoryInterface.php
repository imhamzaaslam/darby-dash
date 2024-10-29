<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use App\Models\Chat;
use App\Models\Project;
use App\Models\User;

interface ChatRepositoryInterface
{
    public function getChatsByProject(Project $project): Collection;
    public function getChatByUser(User $user, Project $project): Collection;
}