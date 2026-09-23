<?php

namespace App\Http\Resources;

use App\Models\LecturaRfid;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LecturaRfid
 */
class LecturaRfidResource extends JsonResource
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
            'tarjeta_rfid_id' => $this->tarjeta_rfid_id,
            'usuario_id' => $this->usuario_id,
            'locker_id' => $this->locker_id,
            'dispositivo_id' => $this->dispositivo_id,
            'tipo_acceso' => $this->tipo_acceso,
            'resultado' => $this->resultado,
            'motivo' => $this->motivo,
            'fecha_hora' => $this->fecha_hora?->toIso8601String(),
            'tarjeta_rfid' => TarjetaRfidResource::make($this->whenLoaded('tarjetaRfid')),
            'usuario' => UserResource::make($this->whenLoaded('usuario')),
            'locker' => LockerResource::make($this->whenLoaded('locker')),
            'dispositivo' => DispositivoResource::make($this->whenLoaded('dispositivo')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
