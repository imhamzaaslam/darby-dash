<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ChatRepositoryInterface;
use App\Models\Base;
use App\Models\Chat;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatRepository extends AbstractEloquentRepository implements ChatRepositoryInterface
{
    /**
     * @var Chat
     */
    protected Base $model;

    public function __construct(Chat $model)
    {
        parent::__construct($model);
    }

    public function getChatsByProject(Project $project): Collection
    {
        $authUserId = auth()->id();
        return $this->model->where('project_id', $project->id)
        ->whereHas('messages', function ($query) use ($authUserId) {
            $query->where('sender_id', $authUserId);
        })->orderBy('created_at', 'asc')->get();
    }

    public function getChat(User $user, Project $project): ?Chat
    {
        $authUserId = auth()->id();

        $chat = $this->model->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->orWhereHas('messages', function ($query) use ($user) {
                $query->where('sender_id', $user->id);
            })->first();

        if($chat)
        {
            $chat->messages()
            ->where('is_seen', false)
            ->where('sender_id', '!=', $authUserId)
            ->update(['is_seen' => true]);
        }

        return $chat;
    }

    public function createByProject(Project $project, User $user): Chat
    {
        return $this->model->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
    }

    public function sendMessage(Project $project, User $user, array $data): void
    {
        $chat = $this->model->where('id', $data['chatId'])->first();
        if($chat)
        {
            $chat->messages()->create([
                'sender_id' => auth()->user()->id,
                'message' => $data['message'],
                'is_delivered' => $user->isOnline() ? true : false,
            ]);
        }
    }

    public function updateMessage(Chat $chat, array $data): void
    {
        $message = $chat->messages()->where('id', $data['chatId'])->first();
        if($message)
        {
            $message->update([
                'message' => $data['message'],
            ]);
        }
    }

    public function deleteMessage(Chat $chat, int $messageId): void
    {
        $message = $chat->messages()->where('id', $messageId)->first();
        if($message)
        {
            $message->delete();
        }
    }
}
