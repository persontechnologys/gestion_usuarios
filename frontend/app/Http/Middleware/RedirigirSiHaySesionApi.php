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
 * Permite el acceso solo a visitantes sin sesión (alias invitado.api).
 */
class RedirigirSiHaySesionApi
{
    /**
     * Recibe el servicio de autenticación mediante inyección de dependencias.
     */
    public function __construct(private AutenticacionService $autenticacionService) {}

    /**
     * Redirige al listado de usuarios cuando ya hay un token de la API.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Comprueba si la sesión ya contiene el token de la API.
        if ($this->autenticacionService->haySesion()) {
            // Redirige al listado de usuarios porque el usuario ya inició sesión.
            return redirect()->route('usuarios.indice');
        }

        // Continúa con la solicitud porque el visitante no tiene sesión.
        return $next($request);
    }
}
