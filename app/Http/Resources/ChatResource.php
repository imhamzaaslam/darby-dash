<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JsonSerializable;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|JsonSerializable|Arrayable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $authUserId = auth()->id();

        $contact = ($this->user_id === $authUserId || $this->contact->id === $authUserId)
        ? new UserResource(getUser($this->messages->first()->sender_id), $this->project_id)
        : new UserResource($this->contact, $this->project_id);

        $lastMessage = $this->messages()->latest()->first();
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'unseen_msgs' => $this->unseen_msgs,
            'contact' => $contact,
            
            'messages' => $this->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'uuid' => $message->uuid,
                    'senderId' => $message->sender_id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'feedback' => [
                        'isSeen' => $message->is_seen,
                        'isDelivered' => $message->is_delivered,
                        'isSent' => $message->is_sent,
                    ],
                ];
            }),
            'last_message' => $lastMessage ?? null,
        ];
    }
}