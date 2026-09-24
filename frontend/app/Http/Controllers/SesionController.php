<?php

namespace App\Http\Controllers;

// Importa la solicitud que valida las credenciales del formulario.
use App\Http\Requests\InicioSesionRequest;
// Importa el servicio que inicia y cierra la sesión en la API.
use App\Services\AutenticacionService;
// Importa la respuesta de redirección.
use Illuminate\Http\RedirectResponse;
// Importa el tipo de las vistas Blade.
use Illuminate\View\View;

/**
 * Muestra el formulario de inicio de sesión, inicia la sesión y la cierra.
 */
class SesionController extends Controller
{
    /**
     * Recibe el servicio de autenticación mediante inyección de dependencias.
     */
    public function __construct(private AutenticacionService $autenticacionService) {}

    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function mostrarFormulario(): View
    {
        // Devuelve la vista con el formulario de credenciales.
        return view('autenticacion.iniciar-sesion');
    }

    /**
     * Inicia sesión en la API con las credenciales validadas.
     */
    public function iniciarSesion(InicioSesionRequest $solicitud): RedirectResponse
    {
        // Envía las credenciales a la API y guarda el token en la sesión.
        $usuario = $this->autenticacionService->iniciarSesion($solicitud->validated());

        // Redirige a la página que se intentaba visitar o al listado de usuarios, con un saludo.
        return redirect()->intended(route('usuarios.indice'))->with('exito', "Bienvenido, {$usuario['nombre']}.");
    }

    /**
     * Cierra la sesión en la API y en el servidor.
     */
    public function cerrarSesion(): RedirectResponse
    {
        // Revoca el token en la API y limpia siempre la sesión local.
        $this->autenticacionService->cerrarSesion();

        // Redirige al formulario de inicio de sesión con un mensaje de confirmación.
        return redirect()->route('sesion.formulario')->with('exito', 'Ha cerrado sesión correctamente.');
    }
}
