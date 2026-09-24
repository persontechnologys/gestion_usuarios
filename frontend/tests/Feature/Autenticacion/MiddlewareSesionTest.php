<?php

// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la fachada de rutas para registrar una ruta protegida de prueba.
use Illuminate\Support\Facades\Route;

// Registra una ruta protegida temporal, porque el módulo de usuarios aún no existe.
beforeEach(function () {
    // Registra una ruta que exige sesión con la API.
    Route::middleware(['web', 'sesion.api'])->get('/prueba/protegida', fn () => 'contenido protegido');
});

// Comprueba que una ruta protegida sin sesión redirige al inicio de sesión.
test('una ruta protegida sin sesión redirige a /iniciar-sesion', function () {
    // Visita la ruta protegida sin token.
    $this->get('/prueba/protegida')
        // Verifica la redirección al formulario de inicio de sesión.
        ->assertRedirect(route('sesion.formulario'));
});

// Comprueba que una ruta protegida con sesión se muestra.
test('una ruta protegida con sesión se muestra', function () {
    // Visita la ruta protegida con un token en la sesión.
    $this->withSession([ClienteApi::CLAVE_TOKEN => 'token-de-prueba'])
        // Solicita la ruta protegida.
        ->get('/prueba/protegida')
        // Verifica el contenido de la ruta.
        ->assertOk()->assertSee('contenido protegido');
});

// Comprueba que los formularios de visitante redirigen al listado si ya hay sesión.
test('los formularios de inicio de sesión y registro redirigen a /usuarios si ya hay sesión', function (string $ruta) {
    // Visita el formulario con un token en la sesión.
    $this->withSession([ClienteApi::CLAVE_TOKEN => 'token-de-prueba'])
        // Solicita el formulario indicado.
        ->get(route($ruta))
        // Verifica la redirección al listado de usuarios.
        ->assertRedirect('/usuarios');
})->with(['sesion.formulario', 'registro.formulario']);

// Comprueba que la raíz redirige según haya sesión o no.
test('la raíz redirige al inicio de sesión sin sesión y a /usuarios con sesión', function () {
    // Verifica la redirección del visitante al inicio de sesión.
    $this->get('/')->assertRedirect(route('sesion.formulario'));

    // Verifica la redirección del usuario autenticado al listado.
    $this->withSession([ClienteApi::CLAVE_TOKEN => 'token-de-prueba'])->get('/')->assertRedirect('/usuarios');
});
