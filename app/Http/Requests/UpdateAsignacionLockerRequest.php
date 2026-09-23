<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAsignacionLockerRequest extends FormRequest
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
            'locker_id' => ['sometimes', 'required', 'integer', 'exists:lockers,id'],
            'usuario_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['nullable', 'date'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['activa', 'finalizada', 'cancelada'])],
        ];
    }
}
