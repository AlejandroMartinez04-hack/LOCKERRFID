<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLockerRequest extends FormRequest
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
            'numero' => ['required', 'string', 'max:50', 'unique:lockers,numero'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => ['nullable', 'string', Rule::in(['disponible', 'ocupado', 'mantenimiento', 'bloqueado'])],
        ];
    }
}
