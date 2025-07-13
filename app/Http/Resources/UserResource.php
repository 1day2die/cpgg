<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'credits' => $this->currencyHelper->convertForDisplay($this->credits),
            'server_limit' => $this->server_limit,
            'pterodactyl_id' => $this->pterodactyl_id,
            'avatar' => $this->avatar,
            'ip' => $this->ip,
            'suspended' => $this->suspended,
            'referral_code' => $this->referral_code,
            'discord_user' => $this->discord_user,
            'email_verified_reward' => $this->email_verified_reward,
            'discord_verified_at' => $this->discord_verified_at,
            'last_seen' => $this->last_seen,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'servers_count' => $this->whenCounted('servers'),
            'servers' => ServerResource::collection($this->whenLoaded('servers')),
            'notifications' => NotificationResource::collection($this->whenLoaded('notifications')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'vouchers' => VoucherResource::collection($this->whenLoaded('vouchers')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
        ];
    }
}
