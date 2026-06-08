<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{

    // AUTORIZACIÓN
    // Define quién tiene permisos para editar a este usuario (Admin o el propio usuario)

    public function authorize(): bool
    {
        $authUser = $this->user();

        // El administrador puede editar a cualquiera
        if ($authUser?->rol === 'administrador') {
            return true;
        }

        // Una entrenadora o gimnasta solo puede editarse a sí misma
        $usuarioEditado = $this->route('usuario');
        return $authUser?->id === $usuarioEditado?->id;
    }


    // REGLAS DE VALIDACIÓN
    // Define las reglas para la actualización (la mayoría usan 'sometimes' al ser opcionales)

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')?->id;
        $usuarioEditado = $this->route('usuario');
        $rol = $this->input('rol') ?? $usuarioEditado?->rol;

        $rules = [
            
            // Datos generales del usuario
            'nombre'    => ['sometimes', 'string', 'max:255'],
            'apellidos' => ['sometimes', 'string', 'max:255'],
            'dni'       => ['sometimes', 'string', 'size:9', 'regex:/^\d{8}[A-Za-z]$/', "unique:users,dni,{$usuarioId}"],
            'email'     => ['sometimes', 'required', 'email', "unique:users,email,{$usuarioId}"],
            'password'  => ['sometimes', 'nullable', Password::min(8)->mixedCase()->numbers()],
            'telefono'  => ['sometimes', 'nullable', 'string', 'max:15'],

            // Campos restringidos (Solo administrador debería poder cambiar esto)
            'rol'       => ['sometimes', 'in:administrador,entrenadora,gimnasta'],
            'activo'    => ['sometimes', 'boolean'],

            // Datos de perfil (Entrenadoras y Gimnastas)
            'club_id'           => ['sometimes', 'integer', 'exists:clubs,id'],
            'titulacion'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'anios_experiencia' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'horas_semanales'   => ['sometimes', 'nullable', 'integer', 'min:0'],
            'estado'            => ['sometimes', 'nullable', 'in:activa,inactiva,baja'],
            'telefono_contacto' => ['sometimes', 'nullable', 'string', 'max:20'],

            // Datos específicos (Solo Gimnastas)
            'categoria_id'     => ['sometimes', 'integer', 'exists:categorias,id'],
            'conjunto_id'      => ['sometimes', 'nullable', 'integer', 'exists:conjuntos,id'],
            'numero_licencia'  => ['sometimes', 'nullable', 'string', "unique:gimnastas,numero_licencia,{$usuarioId},user_id"],
            'fecha_nacimiento' => ['sometimes', 'nullable', 'date', 'before:today'],
            'anios_en_club'    => ['sometimes', 'nullable', 'integer', 'min:0'],

            // Datos del tutor legal (Opcionales por defecto)
            'tutor_nombre'     => ['nullable', 'string', 'max:255'],
            'tutor_apellidos'  => ['nullable', 'string', 'max:255'],
            'tutor_email'      => ['nullable', 'email', 'max:255'],
            'tutor_relacion'   => ['nullable', 'string', 'max:255'],
        ];


        // CONDICIONAL: MENORES DE EDAD
        // Valida la fecha de nacimiento (enviada o existente) para exigir el tutor si es necesario

        $fechaNacimientoRaw = $this->input('fecha_nacimiento') ?? $usuarioEditado?->gimnasta?->fecha_nacimiento;

        if ($rol === 'gimnasta' && $fechaNacimientoRaw) {
            try {
                $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimientoRaw);
                
                if ($fechaNacimiento->age < 18) {
                    $rules['tutor_nombre']    = ['required', 'string', 'max:255'];
                    $rules['tutor_apellidos'] = ['required', 'string', 'max:255'];
                    $rules['tutor_email']     = ['required', 'email', 'max:255'];
                    $rules['tutor_relacion']  = ['required', 'string', 'max:255'];
                }
            } catch (\Exception $e) {
                // Si falla el parseo, la validación principal de fecha se encargará de rechazarlo
            }
        }

        return $rules;
    }


    // MENSAJES DE ERROR PERSONALIZADOS
    // Textos más amigables para los fallos de validación

    public function messages(): array
    {
        return [
            'dni.regex'    => 'El DNI debe tener 8 números seguidos de una letra.',
            'dni.unique'   => 'Ya existe un usuario con este DNI.',
            'email.unique' => 'Ya existe un usuario con este email.',
        ];
    }
}