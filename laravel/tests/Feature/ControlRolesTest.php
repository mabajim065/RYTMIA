<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ControlRolesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $entrenadora;
    private User $gimnasta;

    protected function setUp(): void
    {
        parent::setUp();

        Club::create(['nombre' => 'Club Test', 'email' => 'club@test.com']);

        $this->admin = User::create([
            'nombre' => 'Admin', 'apellidos' => 'Principal',
            'dni' => '00000001A', 'email' => 'admin@test.com',
            'password' => bcrypt('password'), 'rol' => 'administrador', 'activo' => true
        ]);

        $this->entrenadora = User::create([
            'nombre' => 'Ana', 'apellidos' => 'García',
            'dni' => '00000002B', 'email' => 'entrenadora@test.com',
            'password' => bcrypt('password'), 'rol' => 'entrenadora', 'activo' => true
        ]);

        $this->gimnasta = User::create([
            'nombre' => 'Laura', 'apellidos' => 'Pérez',
            'dni' => '00000003C', 'email' => 'gimnasta@test.com',
            'password' => bcrypt('password'), 'rol' => 'gimnasta', 'activo' => true
        ]);
    }

    public function test_gimnasta_no_puede_crear_usuarios(): void
    {
        Sanctum::actingAs($this->gimnasta);
        $response = $this->postJson('/api/usuarios', [
            'nombre' => 'Nueva', 'apellidos' => 'Usuaria',
            'dni' => '99999999Z', 'email' => 'nueva@test.com',
            'rol' => 'gimnasta', 'activo' => true
        ]);
        $response->assertStatus(403);
    }

    public function test_entrenadora_no_puede_crear_usuarios(): void
    {
        Sanctum::actingAs($this->entrenadora);
        $response = $this->postJson('/api/usuarios', [
            'nombre' => 'Nueva', 'apellidos' => 'Usuaria',
            'dni' => '99999999Z', 'email' => 'nueva@test.com',
            'rol' => 'gimnasta', 'activo' => true
        ]);
        $response->assertStatus(403);
    }

    public function test_administrador_puede_listar_usuarios(): void
    {
        Sanctum::actingAs($this->admin);
        $response = $this->getJson('/api/usuarios');
        $response->assertStatus(200);
    }

    public function test_ruta_protegida_sin_token_devuelve_401(): void
    {
        $response = $this->getJson('/api/usuarios');
        $response->assertStatus(401);
    }
}
