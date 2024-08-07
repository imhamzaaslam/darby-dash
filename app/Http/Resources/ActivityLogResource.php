<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "log_name" => $this->log_name,
            "description" => $this->description,
            "subject_type" => $this->subject_type,
            "subject_id" => $this->subject_id,
            "causer_type" => $this->causer_type,
            "causer_id" => $this->causer_id,
            "batch_uuid" => $this->batch_uuid,
            "created_at" => $this->created_at,
        ];
    }
}
