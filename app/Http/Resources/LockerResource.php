<?php

namespace App\Http\Resources;

use App\Models\Locker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Locker
 */
class LockerResource extends JsonResource
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
            'numero' => $this->numero,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'asignacion_activa' => AsignacionLockerResource::make($this->whenLoaded('asignacionActiva')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
