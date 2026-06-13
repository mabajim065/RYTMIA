<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    // permiso para editar usuarios
    public function authorize(): bool
    {
        // usuario logueado
        $authUser = $this->user();

        // el administrador puede editar a cualquiera
        if ($authUser?->rol === 'administrador') {
            return true;
        }

        // usuario que se quiere editar
        $usuarioEditado = $this->route('usuario');

        // cada usuario solo puede editarse a sí mismo
        return $authUser?->id === $usuarioEditado?->id;
    }

    // reglas del formulario
    public function rules(): array
    {
        // id del usuario editado
        $usuarioId = $this->route('usuario')?->id;

        // usuario editado
        $usuarioEditado = $this->route('usuario');

        // rol nuevo o rol actual
        $rol = $this->input('rol') ?? $usuarioEditado?->rol;

        $rules = [

            // datos básicos
            'nombre'    => ['sometimes', 'string', 'max:255'],
            'apellidos' => ['sometimes', 'string', 'max:255'],
            'dni'       => ['sometimes', 'string', 'size:9', 'regex:/^\d{8}[A-Za-z]$/', "unique:users,dni,{$usuarioId}"],
            'email'     => ['sometimes', 'required', 'email', "unique:users,email,{$usuarioId}"],
            'password'  => ['sometimes', 'nullable', Password::min(8)->mixedCase()->numbers()],
            'telefono'  => ['sometimes', 'nullable', 'string', 'max:15'],

            // datos de control
            'rol'       => ['sometimes', 'in:administrador,entrenadora,gimnasta'],
            'activo'    => ['sometimes', 'boolean'],

            // datos de perfil
            'club_id'           => ['sometimes', 'integer', 'exists:clubs,id'],
            'titulacion'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'anios_experiencia' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'horas_semanales'   => ['sometimes', 'nullable', 'integer', 'min:0'],
            'estado'            => ['sometimes', 'nullable', 'in:activa,inactiva,baja'],
            'telefono_contacto' => ['sometimes', 'nullable', 'string', 'max:20'],

            // datos de gimnasta
            'categoria_id'     => ['sometimes', 'integer', 'exists:categorias,id'],
            'conjunto_id'      => ['sometimes', 'nullable', 'integer', 'exists:conjuntos,id'],
            'numero_licencia'  => ['sometimes', 'nullable', 'string', "unique:gimnastas,numero_licencia,{$usuarioId},user_id"],
            'fecha_nacimiento' => ['sometimes', 'nullable', 'date', 'before:today'],
            'anios_en_club'    => ['sometimes', 'nullable', 'integer', 'min:0'],

            // datos del tutor
            'tutor_nombre'     => ['nullable', 'string', 'max:255'],
            'tutor_apellidos'  => ['nullable', 'string', 'max:255'],
            'tutor_email'      => ['nullable', 'email', 'max:255'],
            'tutor_relacion'   => ['nullable', 'string', 'max:255'],
        ];

        // fecha nueva o actual
        $fechaNacimientoRaw = $this->input('fecha_nacimiento') ?? $usuarioEditado?->gimnasta?->fecha_nacimiento;

        // comprobar si es menor
        if ($rol === 'gimnasta' && $fechaNacimientoRaw) {
            try {
                // convertir fecha
                $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimientoRaw);

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

        return $rules;
    }

    // mensajes de error
    public function messages(): array
    {
        return [
            'dni.regex'    => 'el dni debe tener 8 números seguidos de una letra.',
            'dni.unique'   => 'ya existe un usuario con este dni.',
            'email.unique' => 'ya existe un usuario con este email.',
        ];
    }
}