<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }

    public function rules(): array
    {
        return [
            // Campos de usuario
            'nombre'    => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni'       => ['required', 'string', 'size:9', 'regex:/^\d{8}[A-Za-z]$/', 'unique:users,dni'],
            'email'     => ['nullable', 'email', 'unique:users,email'],
            'password'  => ['required', Password::min(8)->mixedCase()->numbers()],
            'rol'       => ['required', 'in:administrador,entrenadora,gimnasta'],
            'telefono'  => ['nullable', 'string', 'max:15'],
            'activo'    => ['boolean'],

            // Perfil entrenadora / gimnasta
            'club_id'           => ['required_unless:rol,administrador', 'integer', 'exists:clubs,id'],
            'titulacion'        => ['nullable', 'string', 'max:255'],
            'anios_experiencia' => ['nullable', 'integer', 'min:0'],
            'horas_semanales'   => ['nullable', 'integer', 'min:0'],

            // Solo gimnasta
            'categoria_id'     => ['required_if:rol,gimnasta', 'integer', 'exists:categorias,id'],
            'conjunto_id'      => ['nullable', 'integer', 'exists:conjuntos,id'],
            'numero_licencia'  => ['nullable', 'string', 'unique:gimnastas,numero_licencia'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'anios_en_club'    => ['nullable', 'integer', 'min:0'],

            // Estado (entrenadora/gimnasta)
            'estado' => ['nullable', 'in:activa,inactiva,baja'],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.regex'          => 'El DNI debe tener 8 números seguidos de una letra.',
            'dni.unique'         => 'Ya existe un usuario con este DNI.',
            'email.unique'       => 'Ya existe un usuario con este email.',
            'club_id.required_unless' => 'El club es obligatorio para entrenadoras y gimnastas.',
            'categoria_id.required_if' => 'La categoría es obligatoria para gimnastas.',
        ];
    }
}
