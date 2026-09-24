<?php

// Declara el espacio de nombres de los servicios del proyecto.

namespace App\Services;

// Importa el modelo de usuarios.
use App\Models\User;
// Importa el repositorio que gestiona el acceso a datos de usuarios.
use App\Repositories\UsuarioRepository;
// Importa el contrato del paginador para tipar el listado paginado.
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Contiene la lógica de negocio de la gestión (CRUD) de usuarios.
 */
class UsuarioService
{
    /**
     * Recibe el repositorio de usuarios mediante inyección de dependencias.
     */
    public function __construct(private UsuarioRepository $usuarioRepository) {}

    /**
     * Obtiene el listado paginado de usuarios.
     */
    public function listarUsuarios(int $cantidadPorPagina = 15): LengthAwarePaginator
    {
        // Solicita al repositorio los usuarios paginados.
        return $this->usuarioRepository->obtenerPaginados($cantidadPorPagina);
    }

    /**
     * Registra un nuevo usuario.
     *
     * @param  array<string, mixed>  $datosUsuario
     */
    public function crearUsuario(array $datosUsuario): User
    {
        // Delega en el repositorio la creación del usuario.
        return $this->usuarioRepository->crear($datosUsuario);
    }

    /**
     * Actualiza la información de un usuario.
     *
     * @param  array<string, mixed>  $datosUsuario
     */
    public function actualizarUsuario(User $usuario, array $datosUsuario): User
    {
        // Comprueba si se envió una contraseña vacía para no sobrescribir la actual.
        if (empty($datosUsuario['contrasena'])) {
            // Descarta la clave de contraseña cuando no se desea modificar.
            unset($datosUsuario['contrasena']);
        }

        // Delega en el repositorio la actualización del usuario.
        return $this->usuarioRepository->actualizar($usuario, $datosUsuario);
    }

    /**
     * Elimina un usuario.
     */
    public function eliminarUsuario(User $usuario): void
    {
        // Delega en el repositorio la eliminación del usuario y sus tokens.
        $this->usuarioRepository->eliminar($usuario);
    }
}
