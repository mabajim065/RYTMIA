<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gimnasta;
use App\Models\Conjunto;
use App\Models\Club;
use App\Models\Categoria;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestGimnastasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurarnos de tener un club de prueba
        $club = Club::firstOrCreate(
            ['nombre' => 'Club Deportivo RYTMIA'],
            ['direccion' => 'Calle Falsa 123', 'telefono' => '600100200', 'email' => 'info@rytmia.com']
        );

        // 2. Asegurarnos de tener una categoría de prueba (ej. Senior)
        $categoria = Categoria::firstOrCreate(
            ['nombre' => 'Senior'],
            ['edad_min' => 16, 'edad_max' => 99]
        );

        // 3. Nombres de los grupos (conjuntos) a crear
        $nombresGrupos = ['Escuela', 'Promesa', 'Precopa', 'Copa'];

        foreach ($nombresGrupos as $nombreGrupo) {
            // Crear el conjunto
            $conjunto = Conjunto::firstOrCreate([
                'nombre' => $nombreGrupo,
                'club_id' => $club->id,
                'categoria_id' => $categoria->id,
            ]);

            // Crear 5 gimnastas para este conjunto
            for ($i = 1; $i <= 5; $i++) {
                $nombreGimnasta = "Gimnasta {$i} {$nombreGrupo}";
                $emailGimnasta = strtolower("gimnasta{$i}_" . str_replace(' ', '', $nombreGrupo) . "@test.com");
                $dniGimnasta = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT) . chr(rand(65, 90));

                // Crear usuario
                $pw = (new \App\Services\UserService())->generarPasswordTemporal("Nombre {$i}", "Apellido {$nombreGrupo}", $dniGimnasta);
                $user = User::firstOrCreate(
                    ['email' => $emailGimnasta],
                    [
                        'nombre' => "Nombre {$i}",
                        'apellidos' => "Apellido {$nombreGrupo}",
                        'dni' => $dniGimnasta,
                        'password' => Hash::make($pw),
                        'password_temporal' => $pw,
                        'rol' => 'gimnasta',
                        'activo' => true
                    ]
                );

                // Crear la ficha de gimnasta y asociarla al conjunto
                Gimnasta::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'club_id' => $club->id,
                        'conjunto_id' => $conjunto->id,
                        'categoria_id' => $categoria->id,
                        'numero_licencia' => 'LIC-' . strtoupper(Str::random(6)),
                        'fecha_nacimiento' => now()->subYears(rand(16, 20))->format('Y-m-d'),
                        'anios_en_club' => rand(1, 5),
                        'estado' => 'activa',
                    ]
                );
            }
        }
    }
}
