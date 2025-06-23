<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $isAdmin = auth()->user()?->isAdmin();

        return [
            'fecha'            => ['required', 'date', 'after:today'],
            'hora'             => ['required', 'date_format:H:i'],
            'descripcion'      => ['required', 'string', 'max:500'],
            'tipo_servicio'    => ['nullable', 'string', 'max:100'],
            'vehiculo_marca'   => ['nullable', 'string', 'max:50'],
            'vehiculo_modelo'  => ['nullable', 'string', 'max:50'],
            'vehiculo_placa'   => ['nullable', 'string', 'max:20'],
            'usuario_id'       => $isAdmin ? ['required', 'exists:users,id'] : ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha es obligatoria',
            'hora.required' => 'La hora es obligatoria',
            'descripcion.required' => 'La descripción es obligatoria',
            'usuario_id.required' => 'El cliente es obligatorio',
        ];
    }
}
