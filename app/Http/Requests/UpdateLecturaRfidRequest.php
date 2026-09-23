<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLecturaRfidRequest extends FormRequest
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
        return [
            'tarjeta_rfid_id' => ['nullable', 'integer', 'exists:tarjetas_rfid,id'],
            'usuario_id' => ['nullable', 'integer', 'exists:users,id'],
            'locker_id' => ['nullable', 'integer', 'exists:lockers,id'],
            'dispositivo_id' => ['sometimes', 'required', 'integer', 'exists:dispositivos,id'],
            'tipo_acceso' => ['sometimes', 'required', 'string', 'max:50'],
            'resultado' => ['sometimes', 'required', 'string', Rule::in(['permitido', 'denegado'])],
            'motivo' => ['sometimes', 'required', 'string', 'max:255'],
            'fecha_hora' => ['sometimes', 'required', 'date'],
        ];
    }
}
