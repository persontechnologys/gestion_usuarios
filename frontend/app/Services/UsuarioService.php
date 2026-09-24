<?php

namespace App\Services;

/**
 * Operaciones CRUD de usuarios contra la API del backend.
 */
class UsuarioService
{
    /**
     * Recibe el cliente de la API mediante inyección de dependencias.
     */
    public function __construct(private ClienteApi $clienteApi) {}

    /**
     * Obtiene una página del listado de usuarios.
     *
     * @return array{data: array<int, array<string, mixed>>, links: array<string, mixed>, meta: array<string, mixed>}
     */
    public function listar(int $pagina = 1): array
    {
        // Solicita a la API la página indicada del listado de usuarios.
        return $this->clienteApi->get('/usuarios', ['page' => $pagina]);
    }

    /**
     * Obtiene los datos de un usuario por su identificador.
     *
     * @return array<string, mixed>
     */
    public function obtener(int $id): array
    {
        // Solicita a la API el usuario indicado.
        $respuesta = $this->clienteApi->get("/usuarios/{$id}");

        // Devuelve el usuario contenido en la clave data.
        return $respuesta['data'];
    }

    /**
     * Crea un usuario nuevo en la API.
     *
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    public function crear(array $datos): array
    {
        // Envía a la API los datos del usuario nuevo.
        $respuesta = $this->clienteApi->post('/usuarios', $datos);

        // Devuelve el usuario creado contenido en la clave data.
        return $respuesta['data'];
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    public function actualizar(int $id, array $datos): array
    {
        // Envía a la API los datos modificados del usuario indicado.
        $respuesta = $this->clienteApi->put("/usuarios/{$id}", $datos);

        // Devuelve el usuario actualizado contenido en la clave data.
        return $respuesta['data'];
    }

    /**
     * Elimina un usuario de la API.
     *
     * @return array{mensaje?: string}
     */
    public function eliminar(int $id): array
    {
        // Solicita a la API la eliminación del usuario indicado.
        return $this->clienteApi->delete("/usuarios/{$id}");
    }
}
