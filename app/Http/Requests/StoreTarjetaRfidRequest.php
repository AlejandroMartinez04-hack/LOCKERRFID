<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTarjetaRfidRequest extends FormRequest
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
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
            'uid' => ['required', 'string', 'max:50', 'unique:tarjetas_rfid,uid'],
            'estado' => ['nullable', 'string', Rule::in(['activa', 'inactiva'])],
        ];
    }
}
