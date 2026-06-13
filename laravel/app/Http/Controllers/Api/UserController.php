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


    // Devuelve una lista paginada de usuarios, con filtros.
    public function index(Request $request): AnonymousResourceCollection
    {
        // Pedimos al servicio que liste usuarios con los filtros (rol, activo, búsqueda).
        $usuarios = $this->userService->listar($request->only(['rol', 'activo', 'search']));

        return UserResource::collection($usuarios);
    }

     // MUESTRA UN USUARIO
    public function show(User $usuario): UserResource
    {
        $usuario->loadMissing(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto']);

        return new UserResource($usuario);
    }

    //lista usuarios por rol
    public function porRol(string $rol): AnonymousResourceCollection
    {
        abort_unless(in_array($rol, ['administrador', 'entrenadora', 'gimnasta']), 422, 'Rol no válido.');

        $usuarios = User::where('rol', $rol)->where('activo', true)->orderBy('apellidos')->get();

        return UserResource::collection($usuarios);
    }


    // CREAR UN NUEVO USUARIO
    public function store(StoreUserRequest $request): JsonResponse
    {
         // Le pedimos al servicio que cree el usuario con los datos validados.
        $usuario = $this->userService->crear($request->validated());

        return (new UserResource($usuario))
            ->response()
            ->setStatusCode(201);
    }

    // actualizar un usuario existente
    public function update(UpdateUserRequest $request, User $usuario): UserResource
    {
        $usuario = $this->userService->actualizar($usuario, $request->validated());

        return new UserResource($usuario);
    }


// Activa o desactiva un usuario
    public function toggleActivo(User $usuario): UserResource
    {
        $usuario->update(['activo' => ! $usuario->activo]);

        return new UserResource($usuario);
    }

   // Elimina un usuario
    public function destroy(Request $request, User $usuario): JsonResponse
    {
        $this->userService->eliminar($usuario, (bool) $request->query('hard', false));

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}