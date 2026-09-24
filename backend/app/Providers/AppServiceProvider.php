<?php

namespace App\Providers;

// Importa la clase que define los límites de peticiones.
use Illuminate\Cache\RateLimiting\Limit;
// Importa la clase de solicitud HTTP.
use Illuminate\Http\Request;
// Importa la fachada para registrar limitadores de peticiones con nombre.
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
// Importa el ayudante de cadenas para normalizar el correo.
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra los limitadores de peticiones de la aplicación.
        $this->configurarLimitadores();
    }

    /**
     * Define el limitador del inicio de sesión de la API.
     *
     * Limita por correo e IP en lugar de solo por IP, porque el frontend BFF (Next.js)
     * reenvía todas las peticiones desde la misma IP de servidor.
     */
    private function configurarLimitadores(): void
    {
        // Registra el limitador "inicio-sesion" usado por la ruta de login.
        RateLimiter::for('inicio-sesion', function (Request $solicitud): Limit {
            // Construye la clave con el correo normalizado en minúsculas y la IP de origen.
            $claveLimite = Str::lower((string) $solicitud->input('correo_electronico')).'|'.$solicitud->ip();

            // Permite 5 intentos por minuto para cada combinación de correo e IP.
            return Limit::perMinute(5)
                // Asocia el límite a la clave construida.
                ->by($claveLimite)
                // Devuelve un mensaje en español cuando se supera el límite.
                ->response(fn (Request $solicitud, array $cabeceras) => response()->json([
                    // Mensaje informativo para el usuario.
                    'message' => 'Demasiados intentos de inicio de sesión. Intente nuevamente en un minuto.',
                ], 429, $cabeceras));
        });
    }
}
