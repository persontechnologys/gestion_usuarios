<?php

// Declara el espacio de nombres de los servicios del proyecto.

namespace App\Services;

// Importa el modelo de usuarios.
use App\Models\User;
// Importa el repositorio que gestiona el acceso a datos de usuarios.
use App\Repositories\UsuarioRepository;
// Importa la fachada de hashing para verificar contraseñas cifradas.
use Illuminate\Support\Facades\Hash;
// Importa la excepción de validación para informar credenciales incorrectas.
use Illuminate\Validation\ValidationException;

/**
 * Contiene la lógica de registro, inicio y cierre de sesión mediante tokens de Sanctum.
 */
class AutenticacionService
{
    /**
     * Nombre asignado a los tokens de acceso emitidos para la API.
     */
    private const NOMBRE_TOKEN = 'token_api';

    /**
     * Recibe el repositorio de usuarios mediante inyección de dependencias.
     */
    public function __construct(private UsuarioRepository $usuarioRepository) {}

    /**
     * Registra un usuario nuevo y le emite un token de acceso.
     *
     * @param  array<string, mixed>  $datosRegistro
     * @return array{usuario: User, token: string}
     */
    public function registrar(array $datosRegistro): array
    {
        // Crea el usuario con los datos validados del registro.
        $usuario = $this->usuarioRepository->crear($datosRegistro);

        // Devuelve el usuario creado junto con su token de acceso en texto plano.
        return [
            // Incluye el usuario registrado.
            'usuario' => $usuario,
            // Genera el token de acceso personal para el nuevo usuario.
            'token' => $usuario->createToken(self::NOMBRE_TOKEN)->plainTextToken,
        ];
    }

    /**
     * Valida las credenciales y emite un token de acceso.
     *
     * @return array{usuario: User, token: string}
     *
     * @throws ValidationException
     */
    public function iniciarSesion(string $correoElectronico, string $contrasena): array
    {
        // Busca el usuario asociado al correo electrónico recibido.
        $usuario = $this->usuarioRepository->buscarPorCorreo($correoElectronico);

        // Comprueba que el usuario exista y que la contraseña coincida con el hash almacenado.
        if ($usuario === null || ! Hash::check($contrasena, $usuario->password)) {
            // Lanza un error de validación genérico para no revelar qué dato es incorrecto.
            throw ValidationException::withMessages([
                // Asocia el mensaje de error al campo de correo electrónico.
                'correo_electronico' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Devuelve el usuario autenticado junto con un nuevo token de acceso.
        return [
            // Incluye el usuario autenticado.
            'usuario' => $usuario,
            // Genera el token de acceso personal para la sesión iniciada.
            'token' => $usuario->createToken(self::NOMBRE_TOKEN)->plainTextToken,
        ];
    }

    /**
     * Revoca el token utilizado en la solicitud actual.
     */
    public function cerrarSesion(User $usuario): void
    {
        // Elimina el token de acceso con el que se autenticó la solicitud actual.
        $usuario->currentAccessToken()->delete();
    }
}
