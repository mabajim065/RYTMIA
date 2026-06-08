<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}


    // LISTADO Y VISUALIZACIÓN
    // Recuperación de usuarios generales, por ID o filtrados por rol

    // GET /api/usuarios (Listado paginado con filtros opcionales: rol, activo, search)
    public function index(Request $request): AnonymousResourceCollection
    {
        $usuarios = $this->userService->listar($request->only(['rol', 'activo', 'search']));

        return UserResource::collection($usuarios);
    }

    // GET /api/usuarios/{usuario} (Detalles del usuario y sus relaciones)
    public function show(User $usuario): UserResource
    {
        $usuario->loadMissing(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto']);

        return new UserResource($usuario);
    }

    // GET /api/usuarios-por-rol/{rol} (Atajo sin paginar para usuarios activos de un rol concreto)
    public function porRol(string $rol): AnonymousResourceCollection
    {
        abort_unless(in_array($rol, ['administrador', 'entrenadora', 'gimnasta']), 422, 'Rol no válido.');

        $usuarios = User::where('rol', $rol)->where('activo', true)->orderBy('apellidos')->get();

        return UserResource::collection($usuarios);
    }


    // CREACIÓN Y EDICIÓN
    // Alta y modificación de credenciales y perfiles asociados

    // POST /api/usuarios (Crea el usuario y su perfil correspondiente según el rol)
    public function store(StoreUserRequest $request): JsonResponse
    {
        $usuario = $this->userService->crear($request->validated());

        return (new UserResource($usuario))
            ->response()
            ->setStatusCode(201);
    }

    // PUT/PATCH /api/usuarios/{usuario} (Actualiza el usuario y su perfil)
    public function update(UpdateUserRequest $request, User $usuario): UserResource
    {
        $usuario = $this->userService->actualizar($usuario, $request->validated());

        return new UserResource($usuario);
    }


    // ESTADO Y ELIMINACIÓN
    // Activar/desactivar accesos y borrado del sistema

    // PATCH /api/usuarios/{usuario}/toggle-activo (Alterna entre cuenta activa o suspendida)
    public function toggleActivo(User $usuario): UserResource
    {
        $usuario->update(['activo' => ! $usuario->activo]);

        return new UserResource($usuario);
    }

    // DELETE /api/usuarios/{usuario} (Borrado lógico por defecto, o físico usando ?hard=1)
    public function destroy(Request $request, User $usuario): JsonResponse
    {
        $this->userService->eliminar($usuario, (bool) $request->query('hard', false));

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}