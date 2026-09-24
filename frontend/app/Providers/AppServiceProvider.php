<?php

namespace App\Providers;

// Importa el servicio de autenticación para leer la clave del usuario en la sesión.
use App\Services\AutenticacionService;
use Illuminate\Support\Facades\Route;
// Importa la fachada de vistas para registrar el compositor del layout.
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
// Importa el tipo de vista que recibe el compositor.
use Illuminate\View\View as Vista;

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
     * Configura los servicios de la aplicación al arrancar.
     *
     * Traduce al español los verbos de las rutas de recursos para que
     * las URL queden como /usuarios/crear y /usuarios/{usuario}/editar, y
     * comparte con el layout principal los datos del usuario autenticado.
     */
    public function boot(): void
    {
        // Sustituye los segmentos create y edit de las rutas de recursos por crear y editar.
        Route::resourceVerbs(['create' => 'crear', 'edit' => 'editar']);

        // Entrega al layout principal el id, el nombre y el correo guardados en la sesión, o null si no hay sesión.
        View::composer('layouts.aplicacion', fn (Vista $vista) => $vista->with('usuarioAutenticado', session(AutenticacionService::CLAVE_USUARIO)));
    }
}
