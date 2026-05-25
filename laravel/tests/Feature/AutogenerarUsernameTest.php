<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Club;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AutogenerarUsernameTest extends TestCase
{
    use RefreshDatabase;

    private Club $club;
    private Categoria $categoria;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->club = Club::create([
            'nombre' => 'Club Test',
            'email' => 'club@test.com'
        ]);

        $this->categoria = Categoria::create([
            'nombre' => 'Infantil',
        ]);

        $this->admin = User::create([
            'nombre' => 'Admin',
            'apellidos' => 'Rytmia',
            'dni' => '12345678A',
            'email' => 'admin@rytmia.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'activo' => true
        ]);
    }

    public function test_autogeneracion_de_username_en_creacion_de_usuario(): void
    {
        $user = User::create([
            'nombre' => 'María',
            'apellidos' => 'Abascal Jiménez',
            'dni' => '11111111A',
            'email' => 'maria@example.com',
            'password' => bcrypt('password'),
            'rol' => 'gimnasta',
            'activo' => true
        ]);

        // Debe generar "maria.abascal" a partir del nombre y primer apellido, eliminando acentos
        $this->assertEquals('maria.abascal', $user->username);
    }

    public function test_colision_de_usernames_genera_un_nombre_unico(): void
    {
        $user1 = User::create([
            'nombre' => 'María',
            'apellidos' => 'Abascal',
            'dni' => '11111111A',
            'email' => 'maria1@example.com',
            'password' => bcrypt('password'),
            'rol' => 'gimnasta',
        ]);

        $user2 = User::create([
            'nombre' => 'María',
            'apellidos' => 'Abascal',
            'dni' => '22222222B',
            'email' => 'maria2@example.com',
            'password' => bcrypt('password'),
            'rol' => 'gimnasta',
        ]);

        $this->assertEquals('maria.abascal', $user1->username);
        $this->assertEquals('maria.abascal1', $user2->username);
    }

    public function test_creacion_de_usuario_por_api_sin_password_autogenera_credenciales(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/usuarios', [
            'nombre' => 'Paula',
            'apellidos' => 'Sánchez Torres',
            'dni' => '33333333C',
            'email' => 'paula@example.com',
            'rol' => 'gimnasta',
            'activo' => true,
            'club_id' => $this->club->id,
            'categoria_id' => $this->categoria->id
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.username', 'paula.sanchez');
        $this->assertEquals('pasa333', $response->json('data.password_temporal'));

        // Verificar en BD que existe y se ha guardado con contraseña encriptada
        $usuarioCreado = User::where('email', 'paula@example.com')->firstOrFail();
        $this->assertNotEmpty($usuarioCreado->password);
        $this->assertEquals('pasa333', $usuarioCreado->password_temporal);
    }

    public function test_login_con_username_y_password_correcto(): void
    {
        $user = User::create([
            'nombre' => 'Lucía',
            'apellidos' => 'Martín',
            'dni' => '44444444D',
            'email' => 'lucia@example.com',
            'password' => Hash::make('MiPasswordSeguro123'),
            'rol' => 'gimnasta',
            'activo' => true
        ]);

        // Login fallido con DNI (ya que fue removido de la validación del AuthController)
        $responseFailDni = $this->postJson('/api/login', [
            'dni' => '44444444D',
            'password' => 'MiPasswordSeguro123'
        ]);
        $responseFailDni->assertStatus(422);

        // Login exitoso con Username
        $responseSuccess = $this->postJson('/api/login', [
            'username' => 'lucia.martin',
            'password' => 'MiPasswordSeguro123'
        ]);

        $responseSuccess->assertStatus(200);
        $responseSuccess->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'nombre',
                'apellidos',
                'username',
                'dni',
                'email',
                'rol',
                'telefono'
            ]
        ]);
    }
}
