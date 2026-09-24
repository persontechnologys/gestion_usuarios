<?php

// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa el servicio de usuarios que se prueba.
use App\Services\UsuarioService;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

// Guarda un token en la sesión antes de cada prueba, porque todas las rutas de usuarios están protegidas.
beforeEach(function () {
    // Guarda un token de prueba en la sesión.
    session()->put(ClienteApi::CLAVE_TOKEN, 'token-de-prueba');
});

// Comprueba que el listado solicita la página indicada y devuelve data, links y meta.
test('listar solicita la página indicada con el Bearer', function () {
    // Simula la respuesta paginada de la API.
    Http::fake(['api.prueba/api/usuarios*' => Http::response([
        'data' => [['id' => 1, 'nombre' => 'Ana']],
        'links' => [],
        'meta' => ['current_page' => 3, 'last_page' => 5],
    ])]);

    // Solicita la tercera página.
    $resultado = app(UsuarioService::class)->listar(3);

    // Verifica la URL con el parámetro page y el token enviado.
    Http::assertSent(fn (Request $peticion): bool => $peticion->url() === 'http://api.prueba/api/usuarios?page=3'
        && $peticion->hasHeader('Authorization', 'Bearer token-de-prueba'));

    // Verifica que se devolvió la metainformación de la paginación.
    expect($resultado['meta']['current_page'])->toBe(3);
});

// Comprueba que obtener devuelve el contenido de data.
test('obtener devuelve el usuario solicitado', function () {
    // Simula la respuesta de un usuario.
    Http::fake(['api.prueba/api/usuarios/4' => Http::response(['data' => ['id' => 4, 'nombre' => 'Luis']])]);

    // Verifica que se devolvió el usuario sin la envoltura data.
    expect(app(UsuarioService::class)->obtener(4))->toBe(['id' => 4, 'nombre' => 'Luis']);
});

// Comprueba que crear envía un POST con los datos.
test('crear envía un POST con los datos del usuario', function () {
    // Simula la respuesta 201 de la creación.
    Http::fake(['api.prueba/api/usuarios' => Http::response(['data' => ['id' => 9, 'nombre' => 'Eva']], 201)]);

    // Crea un usuario de prueba.
    $usuario = app(UsuarioService::class)->crear(['nombre' => 'Eva']);

    // Verifica el método y el cuerpo enviados.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'POST' && $peticion['nombre'] === 'Eva');

    // Verifica el usuario devuelto.
    expect($usuario['id'])->toBe(9);
});

// Comprueba que actualizar envía un PUT a la ruta del usuario.
test('actualizar envía un PUT a la ruta del usuario', function () {
    // Simula la respuesta de la actualización.
    Http::fake(['api.prueba/api/usuarios/9' => Http::response(['data' => ['id' => 9, 'nombre' => 'Eva María']])]);

    // Actualiza el nombre del usuario.
    app(UsuarioService::class)->actualizar(9, ['nombre' => 'Eva María']);

    // Verifica el método, la URL y el cuerpo enviados.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'PUT'
        && $peticion->url() === 'http://api.prueba/api/usuarios/9'
        && $peticion['nombre'] === 'Eva María');
});

// Comprueba que eliminar envía un DELETE a la ruta del usuario.
test('eliminar envía un DELETE a la ruta del usuario', function () {
    // Simula la respuesta de la eliminación.
    Http::fake(['api.prueba/api/usuarios/9' => Http::response(['mensaje' => 'Usuario eliminado.'])]);

    // Elimina el usuario y guarda la respuesta.
    $respuesta = app(UsuarioService::class)->eliminar(9);

    // Verifica el método y la URL enviados.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'DELETE' && $peticion->url() === 'http://api.prueba/api/usuarios/9');

    // Verifica el mensaje devuelto por la API.
    expect($respuesta['mensaje'])->toBe('Usuario eliminado.');
});
