<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Company;

class CompanyResource extends JsonResource
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
            'name' => ucwords($this->name),
            'created_at' => $this->created_at,
            'total_companies' => Company::count(),
            'url' => $this->makeDomainUrl(),
            'admin' => $this->getClient() ? new UserResource($this->getClient()) : null,
        ];
    }
}