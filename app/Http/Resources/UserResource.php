<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name_first' => $this->name_first,
            'name_last' => $this->name_last,
            'email' => $this->email,
            'roles' => $this->getRoleNames(),
            'state' => $this->state,
            'avatar' => $this->avatar,
        ];
    }
}
