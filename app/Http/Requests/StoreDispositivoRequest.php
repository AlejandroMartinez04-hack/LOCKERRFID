<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDispositivoRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:255'],
            'ubicacion' => ['required', 'string', 'max:255'],
            'token' => ['required', 'string', 'max:255', 'unique:dispositivos,token'],
            'estado' => ['nullable', 'string', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
