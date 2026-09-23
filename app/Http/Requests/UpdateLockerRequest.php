<?php

namespace App\Http\Requests;

use App\Models\Locker;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLockerRequest extends FormRequest
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
        $locker = $this->route('locker');
        $lockerId = $locker instanceof Locker ? $locker->id : $locker;

        return [
            'numero' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('lockers', 'numero')->ignore($lockerId)],
            'ubicacion' => ['sometimes', 'required', 'string', 'max:255'],
            'estado' => ['sometimes', 'required', 'string', Rule::in(['disponible', 'ocupado', 'mantenimiento', 'bloqueado'])],
        ];
    }
}
