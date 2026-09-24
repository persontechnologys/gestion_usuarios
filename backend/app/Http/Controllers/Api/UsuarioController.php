<?php

// Declara el espacio de nombres de los controladores de la API.

namespace App\Http\Controllers\Api;

// Importa el controlador base de la aplicación.
use App\Http\Controllers\Controller;
// Importa la solicitud que valida la actualización de usuarios.
use App\Http\Requests\Api\ActualizarUsuarioRequest;
// Importa la solicitud que valida la creación de usuarios.
use App\Http\Requests\Api\GuardarUsuarioRequest;
// Importa el recurso que da formato JSON a los usuarios.
use App\Http\Resources\UsuarioResource;
// Importa el modelo de usuarios.
use App\Models\User;
// Importa el servicio con la lógica de gestión de usuarios.
use App\Services\UsuarioService;
// Importa la clase de respuestas JSON.
use Illuminate\Http\JsonResponse;
// Importa la clase de solicitud HTTP.
use Illuminate\Http\Request;
// Importa la colección de recursos para respuestas paginadas.
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Gestiona el CRUD de usuarios de la API.
 */
class UsuarioController extends Controller
{
    /**
     * Recibe el servicio de usuarios mediante inyección de dependencias.
     */
    public function __construct(private UsuarioService $usuarioService) {}

    /**
     * Lista los usuarios de forma paginada.
     */
    public function index(Request $solicitud): AnonymousResourceCollection
    {
        // Obtiene la cantidad por página solicitada, limitada entre 1 y 100.
        $cantidadPorPagina = min(max($solicitud->integer('por_pagina', 15), 1), 100);

        // Devuelve la colección paginada de usuarios con formato JSON.
        return UsuarioResource::collection($this->usuarioService->listarUsuarios($cantidadPorPagina));
    }

    /**
     * Crea un usuario nuevo.
     */
    public function store(GuardarUsuarioRequest $solicitud): JsonResponse
    {
        // Crea el usuario con los datos validados.
        $usuario = $this->usuarioService->crearUsuario($solicitud->validated());

        // Devuelve el usuario creado con el código HTTP 201 (creado).
        return (new UsuarioResource($usuario))->response()->setStatusCode(201);
    }

    /**
     * Muestra un usuario específico.
     */
    public function show(User $usuario): UsuarioResource
    {
        // Devuelve el usuario resuelto por el enlace de modelo de la ruta.
        return new UsuarioResource($usuario);
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(ActualizarUsuarioRequest $solicitud, User $usuario): UsuarioResource
    {
        // Actualiza el usuario con los datos validados.
        $usuarioActualizado = $this->usuarioService->actualizarUsuario($usuario, $solicitud->validated());

        // Devuelve el usuario actualizado con formato JSON.
        return new UsuarioResource($usuarioActualizado);
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(User $usuario): JsonResponse
    {
        // Elimina el usuario junto con sus tokens de acceso.
        $this->usuarioService->eliminarUsuario($usuario);

        // Devuelve la confirmación de la eliminación.
        return response()->json(['mensaje' => 'Usuario eliminado correctamente.']);
    }
}
