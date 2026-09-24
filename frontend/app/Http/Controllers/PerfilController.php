<?php

namespace App\Http\Controllers;

// Importa el servicio que obtiene el perfil del usuario autenticado.
use App\Services\AutenticacionService;
// Importa el tipo de las vistas Blade.
use Illuminate\View\View;

/**
 * Muestra el perfil del usuario autenticado.
 */
class PerfilController extends Controller
{
    /**
     * Recibe el servicio de autenticación mediante inyección de dependencias.
     */
    public function __construct(private AutenticacionService $autenticacionService) {}

    /**
     * Muestra los datos del usuario obtenidos de /mi-perfil.
     */
    public function mostrar(): View
    {
        // Devuelve la vista con el perfil obtenido de la API.
        return view('perfil.mostrar', ['usuario' => $this->autenticacionService->obtenerMiPerfil()]);
    }
}
