<?php

namespace App\Http\Controllers;

// Importa la solicitud que valida los datos del registro.
use App\Http\Requests\RegistroRequest;
// Importa el servicio que registra al usuario en la API.
use App\Services\AutenticacionService;
// Importa la respuesta de redirección.
use Illuminate\Http\RedirectResponse;
// Importa el tipo de las vistas Blade.
use Illuminate\View\View;

/**
 * Muestra el formulario de registro y crea la cuenta en la API.
 */
class RegistroController extends Controller
{
    /**
     * Recibe el servicio de autenticación mediante inyección de dependencias.
     */
    public function __construct(private AutenticacionService $autenticacionService) {}

    /**
     * Muestra el formulario de registro.
     */
    public function mostrarFormulario(): View
    {
        // Devuelve la vista con el formulario de registro.
        return view('autenticacion.registro');
    }

    /**
     * Registra al usuario en la API y deja su sesión iniciada.
     */
    public function guardar(RegistroRequest $solicitud): RedirectResponse
    {
        // Envía los datos validados a la API y guarda el token recibido en la sesión.
        $usuario = $this->autenticacionService->registrar($solicitud->validated());

        // Redirige al listado de usuarios con un mensaje de bienvenida.
        return redirect()->route('usuarios.indice')->with('exito', "Su cuenta se creó correctamente. Bienvenido, {$usuario['nombre']}.");
    }
}
