<?php

namespace App\Http\Resources;

use App\Models\TarjetaRfid;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TarjetaRfid
 */
class TarjetaRfidResource extends JsonResource
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
            'usuario_id' => $this->usuario_id,
            'uid' => $this->uid,
            'estado' => $this->estado,
            'usuario' => UserResource::make($this->whenLoaded('usuario')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
