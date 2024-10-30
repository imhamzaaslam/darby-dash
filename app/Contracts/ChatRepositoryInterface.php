<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use App\Models\Chat;
use App\Models\Project;
use App\Models\User;

interface ChatRepositoryInterface
{
    public function getChatsByProject(Project $project): Collection;
    public function getChat(User $user, Project $project): ?Chat;
    public function createByProject(Project $project, User $user): Chat;
    public function sendMessage(Project $project, User $user, array $data): void;
    public function updateMessage(Chat $chat, array $data): void;
    public function deleteMessage(Chat $chat, int $messageId): void;
}