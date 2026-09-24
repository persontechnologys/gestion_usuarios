<?php

// Declara el espacio de nombres de los repositorios del proyecto.

namespace App\Repositories;

// Importa el modelo de usuarios utilizado por la autenticación de Laravel.
use App\Models\User;
// Importa el contrato del paginador para tipar el listado paginado.
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Encapsula el acceso a datos de la tabla de usuarios mediante Eloquent.
 */
class UsuarioRepository
{
    /**
     * Relaciona los campos en español de la API con las columnas internas de Laravel.
     *
     * @var array<string, string>
     */
    private const COLUMNAS_POR_CAMPO = [
        // El nombre se guarda en la columna "name".
        'nombre' => 'name',
        // El correo se guarda en la columna "email", usada por la autenticación.
        'correo_electronico' => 'email',
        // La contraseña se guarda en la columna "password", cifrada por el cast "hashed".
        'contrasena' => 'password',
    ];

    /**
     * Obtiene los usuarios paginados, ordenados del más reciente al más antiguo.
     */
    public function obtenerPaginados(int $cantidadPorPagina = 15): LengthAwarePaginator
    {
        // Consulta los usuarios ordenados por fecha de creación descendente y los pagina.
        return User::query()->latest()->paginate($cantidadPorPagina);
    }

    /**
     * Busca un usuario por su correo electrónico.
     */
    public function buscarPorCorreo(string $correoElectronico): ?User
    {
        // Consulta el primer usuario cuyo correo coincide con el indicado.
        return User::query()->where('email', $correoElectronico)->first();
    }

    /**
     * Crea un nuevo usuario con los datos proporcionados.
     *
     * @param  array<string, mixed>  $datosUsuario
     */
    public function crear(array $datosUsuario): User
    {
        // Inserta el registro del usuario; la contraseña se cifra mediante el cast "hashed" del modelo.
        return User::query()->create($this->mapearAColumnas($datosUsuario));
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * @param  array<string, mixed>  $datosUsuario
     */
    public function actualizar(User $usuario, array $datosUsuario): User
    {
        // Asigna y guarda los nuevos valores del usuario en la base de datos.
        $usuario->update($this->mapearAColumnas($datosUsuario));

        // Devuelve el usuario con los valores recargados desde la base de datos.
        return $usuario->refresh();
    }

    /**
     * Elimina un usuario de la base de datos.
     */
    public function eliminar(User $usuario): void
    {
        // Revoca todos los tokens de acceso asociados al usuario.
        $usuario->tokens()->delete();

        // Elimina el registro del usuario.
        $usuario->delete();
    }

    /**
     * Convierte los campos en español recibidos por la API en las columnas de la tabla de usuarios.
     *
     * @param  array<string, mixed>  $datosUsuario
     * @return array<string, mixed>
     */
    private function mapearAColumnas(array $datosUsuario): array
    {
        // Inicializa el arreglo con los datos listos para Eloquent.
        $datosColumnas = [];

        // Recorre cada campo recibido junto con su valor.
        foreach ($datosUsuario as $campo => $valor) {
            // Asigna el valor a su columna equivalente o conserva el campo si ya coincide con la columna.
            $datosColumnas[self::COLUMNAS_POR_CAMPO[$campo] ?? $campo] = $valor;
        }

        // Devuelve los datos con los nombres de columna correctos.
        return $datosColumnas;
    }
}
