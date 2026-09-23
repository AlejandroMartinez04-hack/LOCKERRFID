<?php

namespace App\Http\Requests;

use App\Models\TarjetaRfid;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTarjetaRfidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tarjeta = $this->route('tarjetas_rfid') ?? $this->route('tarjeta_rfid') ?? $this->route('tarjeta');
        $tarjetaId = $tarjeta instanceof TarjetaRfid ? $tarjeta->id : $tarjeta;

        return [
            'usuario_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'uid' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('tarjetas_rfid', 'uid')->ignore($tarjetaId)],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activa', 'inactiva'])],
        ];
    }
}
