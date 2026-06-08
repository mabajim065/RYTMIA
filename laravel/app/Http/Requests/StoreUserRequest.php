<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{

    // AUTORIZACIÓN
    // Solo los administradores tienen permiso para crear nuevos usuarios

    public function authorize(): bool
    {
        return $this->user()?->rol === 'administrador';
    }


    // REGLAS DE VALIDACIÓN
    // Define los requisitos de cada campo dependiendo del rol seleccionado

    public function rules(): array
    {
        $rules = [
            
            // Datos generales del usuario
            'nombre'    => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni'       => ['required', 'string', 'size:9', 'regex:/^\d{8}[A-Za-z]$/', 'unique:users,dni'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'rol'       => ['required', 'in:administrador,entrenadora,gimnasta'],
            'telefono'  => ['nullable', 'string', 'max:15'],
            'activo'    => ['boolean'],

            // Datos de perfil (Entrenadoras y Gimnastas)
            'club_id'           => ['required_unless:rol,administrador', 'integer', 'exists:clubs,id'],
            'titulacion'        => ['nullable', 'string', 'max:255'],
            'anios_experiencia' => ['nullable', 'integer', 'min:0'],
            'horas_semanales'   => ['nullable', 'integer', 'min:0'],
            'estado'            => ['nullable', 'in:activa,inactiva,baja'],
            'telefono_contacto' => ['nullable', 'string', 'max:20'],

            // Datos específicos (Solo Gimnastas)
            'categoria_id'     => ['required_if:rol,gimnasta', 'integer', 'exists:categorias,id'],
            'conjunto_id'      => ['nullable', 'integer', 'exists:conjuntos,id'],
            'numero_licencia'  => ['nullable', 'string', 'unique:gimnastas,numero_licencia'],
            'fecha_nacimiento' => ['required_if:rol,gimnasta', 'nullable', 'date', 'before:today'],
            'anios_en_club'    => ['nullable', 'integer', 'min:0'],

            // Datos del tutor legal (Opcionales por defecto)
            'tutor_nombre'     => ['nullable', 'string', 'max:255'],
            'tutor_apellidos'  => ['nullable', 'string', 'max:255'],
            'tutor_email'      => ['nullable', 'email', 'max:255'],
            'tutor_relacion'   => ['nullable', 'string', 'max:255'],
        ];


        // CONDICIONAL: MENORES DE EDAD
        // Si es gimnasta y menor de 18 años, los datos del tutor pasan a ser obligatorios

        if ($this->input('rol') === 'gimnasta' && $this->filled('fecha_nacimiento')) {
            try {
                $fechaNacimiento = \Carbon\Carbon::parse($this->input('fecha_nacimiento'));
                
                if ($fechaNacimiento->age < 18) {
                    $rules['tutor_nombre']   = ['required', 'string', 'max:255'];
                    $rules['tutor_apellidos']= ['required', 'string', 'max:255'];
                    $rules['tutor_email']    = ['required', 'email', 'max:255'];
                    $rules['tutor_relacion'] = ['required', 'string', 'max:255'];
                }
            } catch (\Exception $e) {
                // Si la fecha tiene un formato inválido, la validación base de 'date' lo capturará
            }
        }

        return $rules;
    }


    // MENSAJES DE ERROR PERSONALIZADOS
    // Textos más amigables para los fallos de validación más comunes

    public function messages(): array
    {
        return [
            'dni.regex'                => 'El DNI debe tener 8 números seguidos de una letra.',
            'dni.unique'               => 'Ya existe un usuario con este DNI.',
            'email.unique'             => 'Ya existe un usuario con este email.',
            'club_id.required_unless'  => 'El club es obligatorio para entrenadoras y gimnastas.',
            'categoria_id.required_if' => 'La categoría es obligatoria para gimnastas.',
        ];
    }
}