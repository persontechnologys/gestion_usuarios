<?php

namespace App\Services;

// Importa la excepción que indica que la API no está disponible.
use App\Exceptions\ApiNoDisponibleException;
// Importa la excepción que indica que el token de la API caducó.
use App\Exceptions\SesionExpiradaException;
// Importa el ayudante de arreglos para extraer solo algunas claves.
use Illuminate\Support\Arr;
// Importa la excepción de validación que la API puede provocar con un 422 o 429.
use Illuminate\Validation\ValidationException;
// Importa la interfaz de las excepciones HTTP lanzadas por abort().
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Gestiona la autenticación contra la API y el estado de la sesión local.
 *
 * El token de Sanctum y los datos básicos del usuario se guardan solo en la
 * sesión del servidor; nunca se envían al navegador.
 */
class AutenticacionService
{
    /**
     * Clave de la sesión donde se guardan los datos básicos del usuario autenticado.
     */
    public const CLAVE_USUARIO = 'usuario_api';

    /**
     * Recibe el cliente de la API mediante inyección de dependencias.
     */
    public function __construct(private ClienteApi $clienteApi) {}

    /**
     * Inicia sesión en la API y guarda el token en la sesión del servidor.
     *
     * @param  array{correo_electronico: string, contrasena: string}  $credenciales
     * @return array<string, mixed>
     */
    public function iniciarSesion(array $credenciales): array
    {
        // Envía las credenciales a la API para obtener el token de acceso.
        $respuesta = $this->clienteApi->post('/iniciar-sesion', $credenciales);

        // Guarda el token y los datos del usuario, y regenera la sesión.
        $this->guardarSesion($respuesta);

        // Devuelve los datos del usuario autenticado.
        return $respuesta['usuario'];
    }

    /**
     * Registra un usuario nuevo en la API y deja su sesión iniciada.
     *
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    public function registrar(array $datos): array
    {
        // Envía los datos del registro a la API, que devuelve el usuario y su token.
        $respuesta = $this->clienteApi->post('/registro', $datos);

        // Guarda el token y los datos del usuario, y regenera la sesión.
        $this->guardarSesion($respuesta);

        // Devuelve los datos del usuario registrado.
        return $respuesta['usuario'];
    }

    /**
     * Cierra la sesión en la API y siempre limpia la sesión local.
     */
    public function cerrarSesion(): void
    {
        // Intenta revocar el token en la API.
        try {
            // Comprueba si hay un token que revocar antes de llamar a la API.
            if ($this->haySesion()) {
                // Solicita a la API que revoque el token actual.
                $this->clienteApi->post('/cerrar-sesion');
            }
        } catch (SesionExpiradaException|ApiNoDisponibleException|ValidationException|HttpExceptionInterface) {
            // Ignora cualquier error de la API: la sesión local se cierra de todos modos.
        } finally {
            // Limpia la sesión local aunque la API haya fallado.
            $this->cerrarSesionLocal();
        }
    }

    /**
     * Cierra la sesión solo en el servidor, sin llamar a la API.
     *
     * Se usa cuando el token ya no existe en la API, por ejemplo tras eliminar la propia cuenta.
     */
    public function cerrarSesionLocal(): void
    {
        // Elimina el token y los datos del usuario de la sesión.
        $this->olvidarSesion();

        // Invalida la sesión actual y genera un identificador nuevo.
        session()->invalidate();

        // Regenera el token CSRF para que no pueda reutilizarse el anterior.
        session()->regenerateToken();
    }

    /**
     * Indica si el identificador corresponde al usuario de la sesión actual.
     */
    public function esUsuarioAutenticado(int $id): bool
    {
        // Compara el id recibido con el id guardado en la sesión.
        return (int) session(self::CLAVE_USUARIO.'.id') === $id;
    }

    /**
     * Guarda en la sesión el id, el nombre y el correo del usuario autenticado.
     *
     * @param  array<string, mixed>  $usuario
     */
    public function recordarUsuario(array $usuario): void
    {
        // Guarda solo el id, el nombre y el correo del usuario para mostrarlos en el layout.
        session()->put(self::CLAVE_USUARIO, Arr::only($usuario, ['id', 'nombre', 'correo_electronico']));
    }

    /**
     * Obtiene el perfil completo del usuario autenticado desde la API.
     *
     * @return array<string, mixed>
     */
    public function obtenerMiPerfil(): array
    {
        // Solicita a la API los datos del usuario dueño del token.
        $respuesta = $this->clienteApi->get('/mi-perfil');

        // Devuelve el usuario contenido en la clave data.
        return $respuesta['data'];
    }

    /**
     * Indica si hay un token de la API guardado en la sesión.
     */
    public function haySesion(): bool
    {
        // Comprueba si la sesión contiene el token de acceso.
        return session()->has(ClienteApi::CLAVE_TOKEN);
    }

    /**
     * Elimina de la sesión el token y los datos del usuario sin invalidarla.
     */
    public function olvidarSesion(): void
    {
        // Borra el token de acceso y los datos básicos del usuario.
        session()->forget([ClienteApi::CLAVE_TOKEN, self::CLAVE_USUARIO]);
    }

    /**
     * Guarda el token y los datos básicos del usuario, y regenera la sesión.
     *
     * @param  array{usuario: array<string, mixed>, token: string}  $respuesta
     */
    private function guardarSesion(array $respuesta): void
    {
        // Regenera el identificador de sesión para prevenir la fijación de sesión.
        session()->regenerate();

        // Guarda el token de acceso en la sesión del servidor.
        session()->put(ClienteApi::CLAVE_TOKEN, $respuesta['token']);

        // Guarda los datos básicos del usuario autenticado.
        $this->recordarUsuario($respuesta['usuario']);
    }
}
