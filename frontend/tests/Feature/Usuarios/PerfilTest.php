<?php

// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

// Comprueba que el perfil muestra los datos devueltos por /mi-perfil.
test('el perfil muestra los datos de /mi-perfil', function () {
    // Simula la respuesta del perfil del usuario autenticado.
    Http::fake(['api.prueba/api/mi-perfil' => Http::response(['data' => usuarioApi(['id' => 1, 'nombre' => 'Ana Pérez', 'correo_electronico' => 'ana@correo.com'])])]);

    // Visita el perfil con sesión iniciada.
    $this->withSession(sesionApi())
        // Solicita la página del perfil.
        ->get(route('perfil.mostrar'))
        // Verifica que la página carga correctamente.
        ->assertOk()
        // Verifica los datos del perfil.
        ->assertSee(['Mi perfil', 'Ana Pérez', 'ana@correo.com', '0102030405'])
        // Verifica el enlace para editar los propios datos.
        ->assertSee(route('usuarios.editar', 1), false);

    // Verifica que la petición a la API llevó el token.
    Http::assertSent(fn (Request $peticion): bool => $peticion->hasHeader('Authorization', 'Bearer token-de-prueba'));
});

// Comprueba que el perfil exige sesión.
test('el perfil sin sesión redirige al inicio de sesión', function () {
    // Visita el perfil sin token.
    $this->get(route('perfil.mostrar'))->assertRedirect(route('sesion.formulario'));
});
