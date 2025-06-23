<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateCitaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'fecha' => 'sometimes|required|date|after:today',
            'hora' => 'sometimes|required|date_format:H:i',
            'descripcion' => 'sometimes|required|string|max:500',
            'estado' => 'sometimes|required|in:pendiente,confirmada,cancelada',
            'tipo_servicio' => 'nullable|string|max:100',
            'vehiculo_marca' => 'nullable|string|max:50',
            'vehiculo_modelo' => 'nullable|string|max:50',
            'vehiculo_placa' => 'nullable|string|max:20',
            'usuario_id' => auth()->user()->isAdmin() ? 'sometimes|required|exists:users,id' : 'nullable',
        ];
    }

}
