<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'unseen_msgs' => $this->unseen_msgs,
            'contact' => UserResource::collection($this->contact),
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
        ];
    }
}