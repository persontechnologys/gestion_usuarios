<?php

// Importa el servicio de autenticación que se prueba.
use App\Services\AutenticacionService;
// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la excepción que lanza el cliente HTTP cuando no logra conectarse.
use Illuminate\Http\Client\ConnectionException;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;
// Importa la excepción de validación esperada ante credenciales incorrectas.
use Illuminate\Validation\ValidationException;

/**
 * Construye la respuesta que devuelve la API al iniciar sesión o registrarse.
 *
 * @return array<string, mixed>
 */
function respuestaAutenticacion(): array
{
    // Devuelve el cuerpo con el mensaje, el usuario, el token y su tipo.
    return [
        'mensaje' => 'Inicio de sesión exitoso.',
        'usuario' => [
            'id' => 7,
            'nombre' => 'Ana Pérez',
            'correo_electronico' => 'ana@correo.com',
            'cedula' => '1234567890',
            'telefono' => null,
            'direccion' => null,
        ],
        'token' => '7|token-de-prueba',
        'tipo_token' => 'Bearer',
    ];
}

// Comprueba que el inicio de sesión envía las credenciales y guarda el token.
test('iniciarSesion envía las credenciales y guarda el token y el usuario en la sesión', function () {
    // Simula la respuesta correcta del inicio de sesión.
    Http::fake(['api.prueba/api/iniciar-sesion' => Http::response(respuestaAutenticacion())]);

    // Inicia sesión con credenciales de prueba.
    $usuario = app(AutenticacionService::class)->iniciarSesion([
        'correo_electronico' => 'ana@correo.com',
        'contrasena' => 'secreta123',
    ]);

    // Verifica que se envió un POST con las credenciales correctas.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'POST'
        && $peticion->url() === 'http://api.prueba/api/iniciar-sesion'
        && $peticion['correo_electronico'] === 'ana@correo.com'
        && $peticion['contrasena'] === 'secreta123');

    // Verifica que el token quedó en la sesión del servidor.
    expect(session(ClienteApi::CLAVE_TOKEN))->toBe('7|token-de-prueba')
        // Verifica que solo se guardaron el id, el nombre y el correo del usuario.
        ->and(session(AutenticacionService::CLAVE_USUARIO))->toBe(['id' => 7, 'nombre' => 'Ana Pérez', 'correo_electronico' => 'ana@correo.com'])
        // Verifica que el método devuelve el usuario de la API.
        ->and($usuario['cedula'])->toBe('1234567890');
});

// Comprueba que las credenciales incorrectas no guardan ningún token.
test('iniciarSesion no guarda el token cuando la API responde 422', function () {
    // Simula el rechazo de las credenciales.
    Http::fake(['*' => Http::response([
        'message' => 'Las credenciales no son válidas.',
        'errors' => ['correo_electronico' => ['Las credenciales no son válidas.']],
    ], 422)]);

    // Intenta iniciar sesión e ignora la excepción esperada.
    expect(fn () => app(AutenticacionService::class)->iniciarSesion([
        'correo_electronico' => 'ana@correo.com',
        'contrasena' => 'incorrecta',
    ]))->toThrow(ValidationException::class);

    // Verifica que la sesión no contiene token.
    expect(app(AutenticacionService::class)->haySesion())->toBeFalse();
});

// Comprueba que el registro guarda el token devuelto por la API.
test('registrar envía los datos y guarda el token en la sesión', function () {
    // Simula la respuesta 201 del registro.
    Http::fake(['api.prueba/api/registro' => Http::response(respuestaAutenticacion(), 201)]);

    // Registra un usuario con datos de prueba.
    app(AutenticacionService::class)->registrar([
        'nombre' => 'Ana Pérez',
        'correo_electronico' => 'ana@correo.com',
        'cedula' => '1234567890',
        'contrasena' => 'secreta123',
        'contrasena_confirmation' => 'secreta123',
    ]);

    // Verifica que se envió la cédula en la petición de registro.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'POST' && $peticion['cedula'] === '1234567890');

    // Verifica que hay sesión iniciada con el token recibido.
    expect(session(ClienteApi::CLAVE_TOKEN))->toBe('7|token-de-prueba');
});

// Comprueba que el perfil se solicita con el token Bearer.
test('obtenerMiPerfil envía el Bearer y devuelve el usuario', function () {
    // Guarda un token de prueba en la sesión.
    session()->put(ClienteApi::CLAVE_TOKEN, '7|token-de-prueba');

    // Simula la respuesta del perfil.
    Http::fake(['api.prueba/api/mi-perfil' => Http::response(['data' => ['id' => 7, 'nombre' => 'Ana Pérez']])]);

    // Obtiene el perfil del usuario autenticado.
    $perfil = app(AutenticacionService::class)->obtenerMiPerfil();

    // Verifica que la petición llevó el token.
    Http::assertSent(fn (Request $peticion): bool => $peticion->hasHeader('Authorization', 'Bearer 7|token-de-prueba'));

    // Verifica que se devolvió el contenido de data.
    expect($perfil)->toBe(['id' => 7, 'nombre' => 'Ana Pérez']);
});

// Comprueba que cerrar sesión llama a la API y elimina el token.
test('cerrarSesion revoca el token en la API y limpia la sesión', function () {
    // Guarda un token y un usuario de prueba en la sesión.
    session()->put([ClienteApi::CLAVE_TOKEN => '7|token-de-prueba', AutenticacionService::CLAVE_USUARIO => ['id' => 7]]);

    // Simula la respuesta correcta del cierre de sesión.
    Http::fake(['api.prueba/api/cerrar-sesion' => Http::response(['mensaje' => 'Sesión cerrada.'])]);

    // Cierra la sesión.
    app(AutenticacionService::class)->cerrarSesion();

    // Verifica que se llamó a la API con el token.
    Http::assertSent(fn (Request $peticion): bool => $peticion->url() === 'http://api.prueba/api/cerrar-sesion'
        && $peticion->hasHeader('Authorization', 'Bearer 7|token-de-prueba'));

    // Verifica que el token y el usuario ya no están en la sesión.
    expect(session()->has(ClienteApi::CLAVE_TOKEN))->toBeFalse()
        // Verifica que se eliminaron los datos del usuario.
        ->and(session()->has(AutenticacionService::CLAVE_USUARIO))->toBeFalse();
});

// Comprueba que el token se elimina aunque la API falle al cerrar sesión.
test('cerrarSesion elimina el token aunque la API falle', function (Closure $respuestaFallida) {
    // Guarda un token de prueba en la sesión.
    session()->put(ClienteApi::CLAVE_TOKEN, '7|token-de-prueba');

    // Simula el fallo indicado por el conjunto de datos.
    Http::fake($respuestaFallida);

    // Cierra la sesión sin que la excepción de la API se propague.
    app(AutenticacionService::class)->cerrarSesion();

    // Verifica que el token ya no está en la sesión.
    expect(app(AutenticacionService::class)->haySesion())->toBeFalse();
})->with([
    // Simula un error interno de la API.
    'error 500' => fn () => Http::response(['message' => 'Server Error'], 500),
    // Simula un token ya caducado.
    'token caducado 401' => fn () => Http::response(['message' => 'Unauthenticated.'], 401),
    // Simula que la API no responde.
    'sin conexión' => fn () => throw new ConnectionException('Connection refused'),
]);

// Comprueba que cerrar sesión sin token no llama a la API.
test('cerrarSesion no llama a la API cuando no hay token', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Cierra una sesión que no tiene token.
    app(AutenticacionService::class)->cerrarSesion();

    // Verifica que no se envió ninguna petición.
    Http::assertNothingSent();
});
