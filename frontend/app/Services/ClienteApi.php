<?php

namespace App\Services;

// Importa la excepción que indica que la API no está disponible.
use App\Exceptions\ApiNoDisponibleException;
// Importa la excepción que indica que el token de la API caducó o no es válido.
use App\Exceptions\SesionExpiradaException;
// Importa la excepción que lanza el cliente HTTP cuando no logra conectarse.
use Illuminate\Http\Client\ConnectionException;
// Importa el constructor de peticiones HTTP configurables.
use Illuminate\Http\Client\PendingRequest;
// Importa la respuesta devuelta por el cliente HTTP.
use Illuminate\Http\Client\Response;
// Importa la fachada del cliente HTTP de Laravel.
use Illuminate\Support\Facades\Http;
// Importa la excepción de validación para devolver los errores al formulario.
use Illuminate\Validation\ValidationException;

/**
 * Cliente de la API REST del backend.
 *
 * Es la ÚNICA clase del proyecto que usa la fachada Http. Centraliza la URL
 * base, el tiempo de espera, las cabeceras y la conversión de los errores
 * de la API en excepciones que Laravel sabe presentar al usuario.
 */
class ClienteApi
{
    /**
     * Clave de la sesión donde se guarda el token de acceso de la API.
     */
    public const CLAVE_TOKEN = 'token_api';

    /**
     * Envía una petición GET y devuelve el JSON decodificado.
     *
     * @param  array<string, mixed>  $parametros
     * @return array<string, mixed>
     */
    public function get(string $ruta, array $parametros = []): array
    {
        // Envía la petición GET con los parámetros en la cadena de consulta.
        return $this->enviar(fn (PendingRequest $peticion): Response => $peticion->get($ruta, $parametros));
    }

    /**
     * Envía una petición POST con cuerpo JSON y devuelve el JSON decodificado.
     *
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    public function post(string $ruta, array $datos = []): array
    {
        // Envía la petición POST con los datos en el cuerpo JSON.
        return $this->enviar(fn (PendingRequest $peticion): Response => $peticion->post($ruta, $datos));
    }

    /**
     * Envía una petición PUT con cuerpo JSON y devuelve el JSON decodificado.
     *
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    public function put(string $ruta, array $datos = []): array
    {
        // Envía la petición PUT con los datos en el cuerpo JSON.
        return $this->enviar(fn (PendingRequest $peticion): Response => $peticion->put($ruta, $datos));
    }

    /**
     * Envía una petición DELETE y devuelve el JSON decodificado.
     *
     * @return array<string, mixed>
     */
    public function delete(string $ruta): array
    {
        // Envía la petición DELETE a la ruta indicada.
        return $this->enviar(fn (PendingRequest $peticion): Response => $peticion->delete($ruta));
    }

    /**
     * Construye la petición base con URL, tiempo de espera, cabeceras y token.
     */
    private function construirPeticion(): PendingRequest
    {
        // Crea la petición apuntando a la URL base de la API definida en la configuración.
        $peticion = Http::baseUrl(config('services.backend_api.url'))
            // Limita el tiempo de espera de la petición según la configuración.
            ->timeout(config('services.backend_api.timeout'))
            // Indica a la API que la respuesta debe ser JSON.
            ->acceptJson();

        // Obtiene el token de acceso guardado en la sesión del servidor, si existe.
        $token = session()->get(self::CLAVE_TOKEN);

        // Comprueba si hay un token disponible para las rutas protegidas.
        if ($token !== null) {
            // Añade la cabecera Authorization: Bearer <token>.
            $peticion->withToken($token);
        }

        // Devuelve la petición lista para enviarse.
        return $peticion;
    }

    /**
     * Ejecuta la petición y convierte la respuesta o el fallo de conexión.
     *
     * @param  callable(PendingRequest): Response  $ejecutarPeticion
     * @return array<string, mixed>
     *
     * @throws ApiNoDisponibleException
     */
    private function enviar(callable $ejecutarPeticion): array
    {
        // Intenta enviar la petición a la API.
        try {
            // Ejecuta la petición sobre el cliente configurado.
            $respuesta = $ejecutarPeticion($this->construirPeticion());
        } catch (ConnectionException $excepcion) {
            // Convierte el fallo de conexión o de tiempo de espera en una excepción propia.
            throw new ApiNoDisponibleException($excepcion);
        }

        // Procesa el código de estado y devuelve el JSON decodificado.
        return $this->procesarRespuesta($respuesta);
    }

    /**
     * Devuelve el JSON de una respuesta correcta o convierte el error de la API.
     *
     * @return array<string, mixed>
     *
     * @throws SesionExpiradaException
     * @throws ValidationException
     * @throws ApiNoDisponibleException
     */
    private function procesarRespuesta(Response $respuesta): array
    {
        // Comprueba si la API respondió con un código 2xx.
        if ($respuesta->successful()) {
            // Devuelve el cuerpo decodificado o un arreglo vacío si no hay contenido.
            return $respuesta->json() ?? [];
        }

        // Comprueba si el token caducó o no es válido (401).
        if ($respuesta->status() === 401) {
            // Lanza la excepción que cierra la sesión local y redirige al inicio de sesión.
            throw new SesionExpiradaException;
        }

        // Comprueba si el recurso solicitado no existe (404).
        if ($respuesta->status() === 404) {
            // Muestra la página de error 404.
            abort(404);
        }

        // Comprueba si la API rechazó los datos enviados (422).
        if ($respuesta->status() === 422) {
            // Devuelve al formulario los errores de cada campo tal como los envía la API.
            throw ValidationException::withMessages($respuesta->json('errors', []));
        }

        // Comprueba si se superó el límite de intentos de inicio de sesión (429).
        if ($respuesta->status() === 429) {
            // Devuelve al formulario el mensaje de la API bajo el campo del correo electrónico.
            throw ValidationException::withMessages([
                // Usa el mensaje de la API o uno genérico si no lo incluye.
                'correo_electronico' => $respuesta->json('message', 'Demasiados intentos. Intente nuevamente en un minuto.'),
            ]);
        }

        // Comprueba si la API sufrió un error interno (5xx).
        if ($respuesta->serverError()) {
            // Lanza la excepción que informa que el servidor no está disponible.
            throw new ApiNoDisponibleException;
        }

        // Muestra la página de error correspondiente a cualquier otro código de la API.
        abort($respuesta->status());
    }
}
