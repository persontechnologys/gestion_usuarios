<?php

namespace App\Http\Middleware;

// Importa el servicio que consulta si hay un token en la sesión.
use App\Services\AutenticacionService;
// Importa el tipo de la función que continúa la cadena de middleware.
use Closure;
// Importa la solicitud HTTP entrante.
use Illuminate\Http\Request;
// Importa el tipo base de las respuestas HTTP.
use Symfony\Component\HttpFoundation\Response;

/**
 * Permite el acceso solo si hay un token de la API en la sesión (alias sesion.api).
 */
class VerificarSesionApi
{
    /**
     * Recibe el servicio de autenticación mediante inyección de dependencias.
     */
    public function __construct(private AutenticacionService $autenticacionService) {}

    /**
     * Redirige al inicio de sesión cuando no hay token de la API.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Comprueba si la sesión carece del token de la API.
        if (! $this->autenticacionService->haySesion()) {
            // Guarda la URL solicitada y redirige al formulario de inicio de sesión.
            return redirect()->guest(route('sesion.formulario'));
        }

        // Continúa con la solicitud porque hay una sesión activa.
        return $next($request);
    }
}
