<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    // permiso para crear usuarios
    public function authorize(): bool
    {
        // solo puede crear el administrador
        return $this->user()?->rol === 'administrador';
    }

    // reglas del formulario
    public function rules(): array
    {
        $rules = [

            // datos básicos
            'nombre'    => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni'       => ['required', 'string', 'size:9', 'regex:/^\d{8}[A-Za-z]$/', 'unique:users,dni'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'rol'       => ['required', 'in:administrador,entrenadora,gimnasta'],
            'telefono'  => ['nullable', 'string', 'max:15'],
            'activo'    => ['boolean'],

            // datos de perfil
            'club_id'           => ['required_unless:rol,administrador', 'integer', 'exists:clubs,id'],
            'titulacion'        => ['nullable', 'string', 'max:255'],
            'anios_experiencia' => ['nullable', 'integer', 'min:0'],
            'horas_semanales'   => ['nullable', 'integer', 'min:0'],
            'estado'            => ['nullable', 'in:activa,inactiva,baja'],
            'telefono_contacto' => ['nullable', 'string', 'max:20'],

            // datos de gimnasta
            'categoria_id'     => ['required_if:rol,gimnasta', 'integer', 'exists:categorias,id'],
            'conjunto_id'      => ['nullable', 'integer', 'exists:conjuntos,id'],
            'numero_licencia'  => ['nullable', 'string', 'unique:gimnastas,numero_licencia'],
            'fecha_nacimiento' => ['required_if:rol,gimnasta', 'nullable', 'date', 'before:today'],
            'anios_en_club'    => ['nullable', 'integer', 'min:0'],

            // datos del tutor
            'tutor_nombre'     => ['nullable', 'string', 'max:255'],
            'tutor_apellidos'  => ['nullable', 'string', 'max:255'],
            'tutor_email'      => ['nullable', 'email', 'max:255'],
            'tutor_relacion'   => ['nullable', 'string', 'max:255'],
        ];

        // comprobar si es menor
        if ($this->input('rol') === 'gimnasta' && $this->filled('fecha_nacimiento')) {
            try {
                // convertir fecha
                $fechaNacimiento = \Carbon\Carbon::parse($this->input('fecha_nacimiento'));

                // si tiene menos de 18
                if ($fechaNacimiento->age < 18) {

                    // tutor obligatorio
                    $rules['tutor_nombre']    = ['required', 'string', 'max:255'];
                    $rules['tutor_apellidos'] = ['required', 'string', 'max:255'];
                    $rules['tutor_email']     = ['required', 'email', 'max:255'];
                    $rules['tutor_relacion']  = ['required', 'string', 'max:255'];
                }
            } catch (\Exception $e) {
                // si la fecha falla
            }
        }

        // devolver reglas
        return $rules;
    }

    // mensajes de error
    public function messages(): array
    {
        return [
            'dni.regex'                => 'el dni debe tener 8 números seguidos de una letra.',
            'dni.unique'               => 'ya existe un usuario con este dni.',
            'email.unique'             => 'ya existe un usuario con este email.',
            'club_id.required_unless'  => 'el club es obligatorio para entrenadoras y gimnastas.',
            'categoria_id.required_if' => 'la categoría es obligatoria para gimnastas.',
        ];
    }
}