<?php

// Importa la excepción que indica que la API no está disponible.
use App\Exceptions\ApiNoDisponibleException;
// Importa la excepción que indica que el token de la API caducó.
use App\Exceptions\SesionExpiradaException;
// Importa el middleware que redirige a los usuarios que ya tienen sesión.
use App\Http\Middleware\RedirigirSiHaySesionApi;
// Importa el middleware que exige una sesión con la API.
use App\Http\Middleware\VerificarSesionApi;
// Importa el servicio que gestiona la sesión local con la API.
use App\Services\AutenticacionService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// Importa la redirección HTTP devuelta por los manejadores de excepciones.
use Illuminate\Http\RedirectResponse;
// Importa la solicitud HTTP recibida por los manejadores de excepciones.
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registra los alias de los middleware que controlan la sesión con la API.
        $middleware->alias([
            // Exige un token de la API en la sesión.
            'sesion.api' => VerificarSesionApi::class,
            // Exige que no haya un token de la API en la sesión.
            'invitado.api' => RedirigirSiHaySesionApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Evita guardar las contraseñas en la sesión al volver a un formulario con errores.
        $exceptions->dontFlash(['contrasena', 'contrasena_confirmation']);

        // Evita registrar en los logs las sesiones expiradas, que son un caso normal.
        $exceptions->dontReport([SesionExpiradaException::class]);

        // Define la respuesta cuando la API rechaza el token (401).
        $exceptions->render(function (SesionExpiradaException $excepcion): RedirectResponse {
            // Elimina el token y los datos del usuario de la sesión local.
            app(AutenticacionService::class)->olvidarSesion();

            // Redirige al inicio de sesión con el mensaje de sesión expirada.
            return redirect()->route('sesion.formulario')->with('error', $excepcion->getMessage());
        });

        // Define la respuesta cuando la API no responde o devuelve un error 5xx.
        $exceptions->render(function (ApiNoDisponibleException $excepcion, Request $solicitud): ?RedirectResponse {
            // Comprueba si falló la carga de una página: volver atrás podría encadenar redirecciones entre páginas que también fallan.
            if ($solicitud->isMethod('GET')) {
                // Deja que Laravel muestre la página de error 503.
                return null;
            }

            // Vuelve al formulario enviado con los datos escritos (sin contraseñas) y el mensaje de error.
            return redirect()->back()
                // Conserva los valores del formulario excepto las contraseñas.
                ->withInput($solicitud->except(['contrasena', 'contrasena_confirmation']))
                // Guarda el mensaje de error para mostrarlo en la zona de alertas.
                ->with('error', $excepcion->getMessage());
        });
    })->create();
