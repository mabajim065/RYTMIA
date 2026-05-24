<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Club;
use App\Models\Competicion;
use App\Models\Conjunto;
use App\Models\Gimnasta;
use App\Models\Entrenador;
use App\Models\User;
use App\Mail\CompeticionCreadaMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompeticionMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_envio_de_correos_a_gimnastas_y_entrenadoras_al_crear_competicion(): void
    {
        Mail::fake();

        // 1. Crear club y categoría necesarios
        $club = Club::create([
            'nombre' => 'Club Rytmia',
            'direccion' => 'Calle Principal 123',
            'telefono' => '123456789',
            'email' => 'club@rytmia.com'
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Infantil',
            'edad_min' => 10,
            'edad_max' => 12
        ]);

        // 2. Crear administrador
        $admin = User::create([
            'nombre' => 'Admin',
            'apellidos' => 'Rytmia',
            'dni' => '12345678A',
            'email' => 'admin@rytmia.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'activo' => true
        ]);

        // 3. Crear gimnasta 1 (seleccionada directamente)
        $userGimnasta1 = User::create([
            'nombre' => 'Gimnasta',
            'apellidos' => 'Uno',
            'dni' => '11111111A',
            'email' => 'gimnasta1@example.com',
            'password' => bcrypt('password'),
            'rol' => 'gimnasta',
            'activo' => true
        ]);
        $gimnasta1 = Gimnasta::create([
            'user_id' => $userGimnasta1->id,
            'club_id' => $club->id,
            'categoria_id' => $categoria->id,
            'numero_licencia' => 'LIC001',
            'fecha_nacimiento' => '2014-05-10',
            'estado' => 'activa'
        ]);

        // 4. Crear conjunto y gimnasta 2 (seleccionada por conjunto)
        $conjunto = Conjunto::create([
            'nombre' => 'Conjunto Infantil',
            'categoria_id' => $categoria->id,
            'club_id' => $club->id
        ]);

        $userGimnasta2 = User::create([
            'nombre' => 'Gimnasta',
            'apellidos' => 'Dos',
            'dni' => '22222222B',
            'email' => 'gimnasta2@example.com',
            'password' => bcrypt('password'),
            'rol' => 'gimnasta',
            'activo' => true
        ]);
        $gimnasta2 = Gimnasta::create([
            'user_id' => $userGimnasta2->id,
            'club_id' => $club->id,
            'categoria_id' => $categoria->id,
            'conjunto_id' => $conjunto->id,
            'numero_licencia' => 'LIC002',
            'fecha_nacimiento' => '2014-06-15',
            'estado' => 'activa'
        ]);

        // 5. Crear entrenadora (seleccionada directamente)
        $userEntrenadora = User::create([
            'nombre' => 'Entrenadora',
            'apellidos' => 'Prueba',
            'dni' => '33333333C',
            'email' => 'entrenadora@example.com',
            'password' => bcrypt('password'),
            'rol' => 'entrenadora',
            'activo' => true
        ]);
        $entrenadora = Entrenador::create([
            'user_id' => $userEntrenadora->id,
            'club_id' => $club->id,
            'titulacion' => 'Nivel 1',
            'estado' => 'activa'
        ]);

        // Autenticar administrador
        Sanctum::actingAs($admin);

        // Petición de creación de competición
        $response = $this->postJson('/api/competiciones', [
            'nombre' => 'Torneo Nacional de Rítmica',
            'fecha' => '2026-06-20',
            'direccion' => 'Pabellón Central Rytmia',
            'gimnastas' => [$gimnasta1->id],
            'conjuntos' => [$conjunto->id],
            'entrenadoras' => [$entrenadora->id]
        ]);

        $response->assertStatus(201);

        // Verificar que se envió el correo a la gimnasta directa con el asunto correcto
        Mail::assertSent(CompeticionCreadaMail::class, function ($mail) use ($userGimnasta1) {
            return $mail->hasTo($userGimnasta1->email) && 
                   $mail->user->id === $userGimnasta1->id &&
                   str_contains($mail->envelope()->subject, 'Has sido seleccionada');
        });

        // Verificar que se envió el correo a la gimnasta del conjunto con el asunto correcto
        Mail::assertSent(CompeticionCreadaMail::class, function ($mail) use ($userGimnasta2) {
            return $mail->hasTo($userGimnasta2->email) && 
                   $mail->user->id === $userGimnasta2->id &&
                   str_contains($mail->envelope()->subject, 'Has sido seleccionada');
        });

        // Verificar que se envió el correo a la entrenadora directa con el asunto correcto
        Mail::assertSent(CompeticionCreadaMail::class, function ($mail) use ($userEntrenadora) {
            return $mail->hasTo($userEntrenadora->email) && 
                   $mail->user->id === $userEntrenadora->id &&
                   str_contains($mail->envelope()->subject, 'Convocatoria como entrenadora');
        });

        // Verificar que no se enviaron correos al admin
        Mail::assertNotSent(CompeticionCreadaMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }
}
