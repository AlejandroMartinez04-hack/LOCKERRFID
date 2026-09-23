<?php

namespace App\Http\Resources;

use App\Models\AsignacionLocker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AsignacionLocker
 */
class AsignacionLockerResource extends JsonResource
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
            'locker_id' => $this->locker_id,
            'usuario_id' => $this->usuario_id,
            'fecha_inicio' => $this->fecha_inicio?->toIso8601String(),
            'fecha_fin' => $this->fecha_fin?->toIso8601String(),
            'estado' => $this->estado,
            'locker' => LockerResource::make($this->whenLoaded('locker')),
            'usuario' => UserResource::make($this->whenLoaded('usuario')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
