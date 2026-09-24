<?php

// Declara el espacio de nombres de los controladores de la API.

namespace App\Http\Controllers\Api;

// Importa el controlador base de la aplicación.
use App\Http\Controllers\Controller;
// Importa la solicitud que valida las credenciales de inicio de sesión.
use App\Http\Requests\Api\InicioSesionRequest;
// Importa la solicitud que valida los datos de registro.
use App\Http\Requests\Api\RegistroUsuarioRequest;
// Importa el recurso que da formato JSON a los usuarios.
use App\Http\Resources\UsuarioResource;
// Importa el servicio con la lógica de autenticación.
use App\Services\AutenticacionService;
// Importa la clase de respuestas JSON.
use Illuminate\Http\JsonResponse;
// Importa la clase de solicitud HTTP.
use Illuminate\Http\Request;

/**
 * Gestiona el registro, inicio de sesión, cierre de sesión y perfil del usuario autenticado.
 */
class AutenticacionController extends Controller
{
    /**
     * Recibe el servicio de autenticación mediante inyección de dependencias.
     */
    public function __construct(private AutenticacionService $autenticacionService) {}

    /**
     * Registra un usuario nuevo y devuelve su token de acceso.
     */
    public function registrar(RegistroUsuarioRequest $solicitud): JsonResponse
    {
        // Registra el usuario con los datos validados y obtiene su token.
        $resultado = $this->autenticacionService->registrar($solicitud->validated());

        // Devuelve el usuario y el token con el código HTTP 201 (creado).
        return response()->json([
            // Mensaje de confirmación del registro.
            'mensaje' => 'Usuario registrado correctamente.',
            // Datos públicos del usuario registrado.
            'usuario' => new UsuarioResource($resultado['usuario']),
            // Token de acceso en texto plano.
            'token' => $resultado['token'],
            // Tipo de token que debe enviarse en la cabecera Authorization.
            'tipo_token' => 'Bearer',
        ], 201);
    }

    /**
     * Valida las credenciales y devuelve un token de acceso.
     */
    public function iniciarSesion(InicioSesionRequest $solicitud): JsonResponse
    {
        // Autentica al usuario con el correo y la contraseña recibidos.
        $resultado = $this->autenticacionService->iniciarSesion(
            // Correo electrónico validado.
            $solicitud->validated('correo_electronico'),
            // Contraseña validada.
            $solicitud->validated('contrasena'),
        );

        // Devuelve el usuario autenticado y su token.
        return response()->json([
            // Mensaje de confirmación del inicio de sesión.
            'mensaje' => 'Inicio de sesión exitoso.',
            // Datos públicos del usuario autenticado.
            'usuario' => new UsuarioResource($resultado['usuario']),
            // Token de acceso en texto plano.
            'token' => $resultado['token'],
            // Tipo de token que debe enviarse en la cabecera Authorization.
            'tipo_token' => 'Bearer',
        ]);
    }

    /**
     * Revoca el token actual del usuario autenticado.
     */
    public function cerrarSesion(Request $solicitud): JsonResponse
    {
        // Revoca el token utilizado en la solicitud.
        $this->autenticacionService->cerrarSesion($solicitud->user());

        // Devuelve la confirmación del cierre de sesión.
        return response()->json(['mensaje' => 'Sesión cerrada correctamente.']);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function miPerfil(Request $solicitud): UsuarioResource
    {
        // Transforma el usuario autenticado en su representación JSON.
        return new UsuarioResource($solicitud->user());
    }
}
