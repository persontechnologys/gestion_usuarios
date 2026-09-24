<?php

// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

// Comprueba que el detalle muestra los datos del usuario.
test('el detalle muestra los datos del usuario', function () {
    // Simula la respuesta del usuario solicitado.
    Http::fake(['api.prueba/api/usuarios/2' => Http::response(['data' => usuarioApi()])]);

    // Visita el detalle con sesión iniciada.
    $this->withSession(sesionApi())
        // Solicita el detalle del usuario 2.
        ->get(route('usuarios.mostrar', 2))
        // Verifica que la página carga correctamente.
        ->assertOk()
        // Verifica los datos del usuario.
        ->assertSee(['Detalle del usuario', 'Luis Gómez', 'luis@correo.com', '0102030405', 'Av. Principal 123', 'No verificado'])
        // Verifica que el formulario de eliminación pide confirmación.
        ->assertSee('data-confirmar="¿Desea eliminar a Luis Gómez? Esta acción no se puede deshacer."', false);
});

// Comprueba que un usuario inexistente muestra la página de error 404.
test('un 404 de la API muestra la página de error', function () {
    // Simula que la API no encuentra el usuario.
    Http::fake(['*' => Http::response(['message' => 'No encontrado.'], 404)]);

    // Visita el detalle de un usuario inexistente.
    $this->withSession(sesionApi())->get(route('usuarios.mostrar', 999))->assertNotFound();
});

// Comprueba que un identificador no numérico no llega a la API.
test('un identificador no numérico responde 404 sin llamar a la API', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Visita una URL con un identificador inválido.
    $this->withSession(sesionApi())->get('/usuarios/abc')->assertNotFound();

    // Verifica que no se llamó a la API.
    Http::assertNothingSent();
});
