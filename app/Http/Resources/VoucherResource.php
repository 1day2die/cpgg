<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class VoucherResource extends BaseJsonResource
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
            'code' => $this->code,
            'memo' => $this->memo,
            'credits' => $this->currencyHelper->convertForDisplay($this->credits),
            'uses' => $this->uses,
            'expires_at' => $this->expires_at ? $this->expires_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'users' => UserResource::newCollection($this->whenLoaded('users')),
        ];
    }
}
