<?php

// Importa la excepción que indica que la API no está disponible.
use App\Exceptions\ApiNoDisponibleException;
// Importa la excepción que indica que el token de la API caducó.
use App\Exceptions\SesionExpiradaException;
// Importa el cliente de la API que se prueba.
use App\Services\ClienteApi;
// Importa la excepción que lanza el cliente HTTP cuando no logra conectarse.
use Illuminate\Http\Client\ConnectionException;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;
// Importa la excepción de validación esperada ante un 422 o 429.
use Illuminate\Validation\ValidationException;
// Importa la excepción HTTP 404 lanzada por abort(404).
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Comprueba que las peticiones protegidas envían el token Bearer y la cabecera Accept.
test('envía el token Bearer y la cabecera Accept cuando hay sesión', function () {
    // Guarda un token de prueba en la sesión.
    session()->put(ClienteApi::CLAVE_TOKEN, 'token-de-prueba');

    // Simula una respuesta correcta del listado de usuarios.
    Http::fake(['api.prueba/api/usuarios*' => Http::response(['data' => []])]);

    // Ejecuta la petición GET a través del cliente.
    app(ClienteApi::class)->get('/usuarios', ['page' => 2]);

    // Verifica la URL, las cabeceras y el token enviados a la API.
    Http::assertSent(fn (Request $peticion): bool => $peticion->url() === 'http://api.prueba/api/usuarios?page=2'
        && $peticion->hasHeader('Accept', 'application/json')
        && $peticion->hasHeader('Authorization', 'Bearer token-de-prueba'));
});

// Comprueba que sin token no se envía la cabecera Authorization.
test('no envía la cabecera Authorization cuando no hay sesión', function () {
    // Simula una respuesta correcta de la API.
    Http::fake(['*' => Http::response(['mensaje' => 'ok'])]);

    // Ejecuta una petición POST sin token en la sesión.
    app(ClienteApi::class)->post('/iniciar-sesion', ['correo_electronico' => 'ana@correo.com']);

    // Verifica que la petición no lleva la cabecera Authorization.
    Http::assertSent(fn (Request $peticion): bool => ! $peticion->hasHeader('Authorization'));
});

// Comprueba que la respuesta correcta se devuelve como arreglo.
test('devuelve el JSON decodificado como arreglo', function () {
    // Simula una respuesta con un usuario.
    Http::fake(['*' => Http::response(['data' => ['id' => 1, 'nombre' => 'Ana']])]);

    // Ejecuta la petición y guarda el resultado.
    $resultado = app(ClienteApi::class)->get('/usuarios/1');

    // Verifica el contenido devuelto.
    expect($resultado)->toBe(['data' => ['id' => 1, 'nombre' => 'Ana']]);
});

// Comprueba que un 422 se convierte en errores de validación por campo.
test('convierte un 422 en una ValidationException con los errores de la API', function () {
    // Simula una respuesta 422 con errores por campo.
    Http::fake(['*' => Http::response([
        'message' => 'Los datos no son válidos.',
        'errors' => ['correo_electronico' => ['El correo electrónico ya está registrado.']],
    ], 422)]);

    // Ejecuta la petición y captura la excepción esperada.
    try {
        // Envía los datos que la API rechazará.
        app(ClienteApi::class)->post('/usuarios', ['correo_electronico' => 'ana@correo.com']);
        // Falla la prueba si no se lanzó la excepción.
        $this->fail('No se lanzó ValidationException.');
    } catch (ValidationException $excepcion) {
        // Verifica que los errores se conservaron bajo el mismo campo.
        expect($excepcion->errors())->toBe(['correo_electronico' => ['El correo electrónico ya está registrado.']]);
    }
});

// Comprueba que un 429 se convierte en un error en el campo del correo.
test('convierte un 429 en un error de validación en correo_electronico', function () {
    // Simula la respuesta de límite de intentos de la API.
    Http::fake(['*' => Http::response(['message' => 'Demasiados intentos de inicio de sesión. Intente nuevamente en un minuto.'], 429)]);

    // Ejecuta la petición y captura la excepción esperada.
    try {
        // Envía un intento de inicio de sesión.
        app(ClienteApi::class)->post('/iniciar-sesion', ['correo_electronico' => 'ana@correo.com']);
        // Falla la prueba si no se lanzó la excepción.
        $this->fail('No se lanzó ValidationException.');
    } catch (ValidationException $excepcion) {
        // Verifica que el mensaje de la API quedó en el campo del correo.
        expect($excepcion->errors())->toBe(['correo_electronico' => ['Demasiados intentos de inicio de sesión. Intente nuevamente en un minuto.']]);
    }
});

// Comprueba que un 401 lanza la excepción de sesión expirada.
test('lanza SesionExpiradaException ante un 401', function () {
    // Simula una respuesta 401 por token caducado.
    Http::fake(['*' => Http::response(['message' => 'Unauthenticated.'], 401)]);

    // Ejecuta una petición protegida.
    app(ClienteApi::class)->get('/mi-perfil');
})->throws(SesionExpiradaException::class, 'Su sesión ha expirado.');

// Comprueba que un 404 muestra la página de error 404.
test('aborta con 404 cuando la API no encuentra el recurso', function () {
    // Simula una respuesta 404 de la API.
    Http::fake(['*' => Http::response(['message' => 'No encontrado.'], 404)]);

    // Ejecuta la petición de un usuario inexistente.
    app(ClienteApi::class)->get('/usuarios/999');
})->throws(NotFoundHttpException::class);

// Comprueba que un error 5xx lanza la excepción de API no disponible.
test('lanza ApiNoDisponibleException ante un error 500', function () {
    // Simula un error interno de la API.
    Http::fake(['*' => Http::response(['message' => 'Server Error'], 500)]);

    // Ejecuta una petición cualquiera.
    app(ClienteApi::class)->get('/usuarios');
})->throws(ApiNoDisponibleException::class, ApiNoDisponibleException::MENSAJE);

// Comprueba que un fallo de conexión lanza la excepción de API no disponible.
test('lanza ApiNoDisponibleException cuando no hay conexión', function () {
    // Simula que la conexión con la API falla.
    Http::fake(fn () => throw new ConnectionException('Connection refused'));

    // Ejecuta una petición cualquiera.
    app(ClienteApi::class)->get('/usuarios');
})->throws(ApiNoDisponibleException::class, ApiNoDisponibleException::MENSAJE);
