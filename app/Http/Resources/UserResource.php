<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JsonSerializable;

class UserResource extends JsonResource
{
    protected $projectId;

    public function __construct($resource, $projectId = null)
    {
        parent::__construct($resource);
        $this->projectId = $projectId;
    }

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
            'role' => ucwords($this->getRoleNames()->first() ?? ''),
            'state' => $this->state,
            'phone' => $this->info->phone,
            'is_2fa' => $this->is_2fa,
            'is_online' => $this->isOnline(),
            'company' => $this->company->name ?? 'N/A',
            'company_uuid' => $this->company->uuid ?? null,
            'info' => [
                'phone' => $this->info->phone,
                'address' => $this->info->address,
                'city' => $this->info->city,
                'state' => $this->info->state,
                'american_state' => $this->info->american_state,
                'zip' => $this->info->zip,
                'avatar' => $this->avatar,
                'company_name' => $this->info->company_name,
                'company_logo' => $this->info->company_logo ?  asset('images/company_logos/' . $this->info->company_logo) : null,   
            ],
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y h:i:s A'),
            'unseen_messages' => auth()->user()->unseenMessagesFromTeamMember($this->id, $this->projectId) ?? 0,
        ];
    }
}
