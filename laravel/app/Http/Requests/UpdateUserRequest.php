<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = $this->user();

        // El administrador puede editar a cualquiera
        if ($authUser?->rol === 'administrador') {
            return true;
        }

        // Una entrenadora o gimnasta solo puede editarse a sí misma
        // y solo ciertos campos (no el rol, no el estado de activo)
        $usuarioEditado = $this->route('usuario');
        return $authUser?->id === $usuarioEditado?->id;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')?->id;

        return [
            // Campos de usuario (todos opcionales en update)
            'nombre'    => ['sometimes', 'string', 'max:255'],
            'apellidos' => ['sometimes', 'string', 'max:255'],
            'dni'       => ['sometimes', 'string', 'size:9', 'regex:/^\d{8}[A-Za-z]$/', "unique:users,dni,{$usuarioId}"],
            'email'     => ['sometimes', 'nullable', 'email', "unique:users,email,{$usuarioId}"],
            'password'  => ['sometimes', 'nullable', Password::min(8)->mixedCase()->numbers()],
            'telefono'  => ['sometimes', 'nullable', 'string', 'max:15'],

            // Solo administrador puede cambiar estos campos
            'rol'    => ['sometimes', 'in:administrador,entrenadora,gimnasta'],
            'activo' => ['sometimes', 'boolean'],

            // Perfil entrenadora / gimnasta
            'club_id'           => ['sometimes', 'integer', 'exists:clubs,id'],
            'titulacion'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'anios_experiencia' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'horas_semanales'   => ['sometimes', 'nullable', 'integer', 'min:0'],

            // Solo gimnasta
            'categoria_id'     => ['sometimes', 'integer', 'exists:categorias,id'],
            'conjunto_id'      => ['sometimes', 'nullable', 'integer', 'exists:conjuntos,id'],
            'numero_licencia'  => ['sometimes', 'nullable', 'string', "unique:gimnastas,numero_licencia,{$usuarioId},user_id"],
            'fecha_nacimiento' => ['sometimes', 'nullable', 'date', 'before:today'],
            'anios_en_club'    => ['sometimes', 'nullable', 'integer', 'min:0'],

            // Estado
            'estado' => ['sometimes', 'nullable', 'in:activa,inactiva,baja'],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.regex'    => 'El DNI debe tener 8 números seguidos de una letra.',
            'dni.unique'   => 'Ya existe un usuario con este DNI.',
            'email.unique' => 'Ya existe un usuario con este email.',
        ];
    }
}
