<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mensaje;
use App\Models\Competicion;
use App\Models\User;
use App\Models\Conjunto;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios clave
        $admin = User::where('rol', 'administrador')->first();
        $entrenadora = User::where('rol', 'entrenadora')->first();
        $gimnasta = User::where('rol', 'gimnasta')->first();
        
        if (!$admin || !$entrenadora || !$gimnasta) {
            return; // Si no existen los usuarios base, no podemos crear los datos de prueba
        }

        // --- Crear Competiciones de Prueba ---
        $competicion = Competicion::create([
            'nombre' => 'Campeonato de Andalucía Base',
            'fecha' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'direccion' => 'Pabellón Municipal, Sevilla',
            'lat' => 37.3891,
            'lng' => -5.9845,
            'tipo' => 'nacional_base',
            'estado' => 'confirmada',
        ]);

        // Asignar al primer conjunto disponible
        $conjunto = Conjunto::first();
        if ($conjunto) {
            $competicion->conjuntos()->attach($conjunto->id);
            $competicion->entrenadoras()->attach($entrenadora->entrenador->id ?? 1);
        }

        $competicion2 = Competicion::create([
            'nombre' => 'Torneo Promesas Rytmia',
            'fecha' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'direccion' => 'Polideportivo Ciudad Jardín, Málaga',
            'lat' => 36.7213,
            'lng' => -4.4213,
            'tipo' => 'promesas',
            'estado' => 'pendiente',
        ]);

        // --- Crear Mensajes de Prueba ---
        // Gimnasta a Entrenadora
        Mensaje::create([
            'emisor_id' => $gimnasta->id,
            'receptor_id' => $entrenadora->id,
            'asunto' => 'Falta de asistencia el viernes',
            'contenido' => 'Hola, el próximo viernes no podré asistir al entrenamiento porque tengo médico. Un saludo.',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Entrenadora responde a Gimnasta
        Mensaje::create([
            'emisor_id' => $entrenadora->id,
            'receptor_id' => $gimnasta->id,
            'asunto' => 'RE: Falta de asistencia el viernes',
            'contenido' => 'De acuerdo, gracias por avisar. Acuérdate de repasar el ejercicio en casa.',
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Administrador a Entrenadora
        Mensaje::create([
            'emisor_id' => $admin->id,
            'receptor_id' => $entrenadora->id,
            'asunto' => 'Inscripciones cerradas',
            'contenido' => 'Buenas tardes. Ya he cerrado las inscripciones para el próximo campeonato. Revisa que todas tus niñas estén en la lista.',
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(5),
        ]);
        
        // Entrenadora a Administrador
        Mensaje::create([
            'emisor_id' => $entrenadora->id,
            'receptor_id' => $admin->id,
            'asunto' => 'Pedido de maillots',
            'contenido' => 'Hola, ¿cuando llegarán los nuevos maillots del conjunto alevín?',
            'created_at' => Carbon::now()->subHours(1),
            'updated_at' => Carbon::now()->subHours(1),
        ]);
    }
}
