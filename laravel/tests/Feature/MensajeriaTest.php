<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MensajeriaTest extends TestCase
{
    use RefreshDatabase;

    private User $remitente;
    private User $destinatario;

    protected function setUp(): void
    {
        parent::setUp();

        $this->remitente = User::create([
            'nombre' => 'Laura', 'apellidos' => 'Martín',
            'dni' => '11111111A', 'email' => 'laura@test.com',
            'password' => bcrypt('password'), 'rol' => 'gimnasta', 'activo' => true
        ]);

        $this->destinatario = User::create([
            'nombre' => 'Ana', 'apellidos' => 'García',
            'dni' => '22222222B', 'email' => 'ana@test.com',
            'password' => bcrypt('password'), 'rol' => 'entrenadora', 'activo' => true
        ]);
    }

    public function test_usuario_autenticado_puede_enviar_mensaje(): void
    {
        Sanctum::actingAs($this->remitente);
        $response = $this->postJson('/api/mensajes', [
            'receptor_id' => $this->destinatario->id,
            'asunto'      => 'Consulta sobre entrenamiento',
            'contenido'   => 'Hola, ¿a qué hora es el entrenamiento del lunes?'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('mensajes', [
            'emisor_id'   => $this->remitente->id,
            'receptor_id' => $this->destinatario->id,
            'asunto'      => 'Consulta sobre entrenamiento'
        ]);
    }

    public function test_usuario_puede_ver_sus_mensajes(): void
    {
        Sanctum::actingAs($this->destinatario);
        $response = $this->getJson('/api/mensajes');
        $response->assertStatus(200);
    }

    public function test_sin_autenticacion_no_puede_enviar_mensaje(): void
    {
        $response = $this->postJson('/api/mensajes', [
            'receptor_id' => $this->destinatario->id,
            'asunto'      => 'Test',
            'contenido'   => 'Contenido'
        ]);
        $response->assertStatus(401);
    }
}