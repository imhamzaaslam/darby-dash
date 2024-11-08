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
        $firstUser = $this->user();
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => ucwords($this->name),
            'created_at' => $this->created_at,
            'user_uuid' => $firstUser->uuid,
            'name_first' => $firstUser->name_first,
            'name_last' => $firstUser->name_last,
            'email' => $firstUser->email,
            'role' => ucwords($firstUser->getRoleNames()->first() ?? ''),
            'phone' => $firstUser->info->phone,
            'total_companies' => Company::count(),
        ];
    }
}