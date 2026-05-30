<?php

namespace App\Http\Resources;

use App\Models\WordpressSite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WordpressSite
 */
class WordpressSiteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_url' => $this->base_url,
            'username' => $this->username,
            'connector_version' => $this->connector_version,
            'last_connected_at' => $this->last_connected_at,
            'created_at' => $this->created_at,
            // app_password intentionally omitted.
        ];
    }
}
