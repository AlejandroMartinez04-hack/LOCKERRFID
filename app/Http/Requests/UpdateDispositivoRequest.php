<?php

namespace App\Http\Requests;

use App\Models\Dispositivo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDispositivoRequest extends FormRequest
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
        $dispositivo = $this->route('dispositivo');
        $dispositivoId = $dispositivo instanceof Dispositivo ? $dispositivo->id : $dispositivo;

        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'ubicacion' => ['sometimes', 'required', 'string', 'max:255'],
            'token' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('dispositivos', 'token')->ignore($dispositivoId)],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
