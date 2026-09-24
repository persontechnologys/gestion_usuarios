<?php

// Importa la excepción que indica que la API no está disponible.
use App\Exceptions\ApiNoDisponibleException;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;
// Importa la fachada de rutas para registrar rutas de prueba.
use Illuminate\Support\Facades\Route;

// Registra rutas temporales que provocan cada error antes de cada prueba.
beforeEach(function () {
    // Registra una ruta que simula un formulario con el token CSRF caducado.
    Route::middleware('web')->get('/prueba/error-419', fn () => abort(419));

    // Registra una ruta que simula un error inesperado de la aplicación.
    Route::middleware('web')->get('/prueba/error-500', fn () => throw new RuntimeException('Fallo de prueba'));

    // Registra una ruta que simula que la API no responde al cargar una página.
    Route::middleware('web')->get('/prueba/error-503', fn () => throw new ApiNoDisponibleException);
});

// Comprueba que una URL inexistente muestra la página 404 en español.
test('una URL inexistente muestra la página 404 en español', function () {
    // Visita una URL que no existe.
    $this->get('/pagina-inexistente')
        // Verifica el código y los textos en español.
        ->assertNotFound()->assertSee(['Página no encontrada', 'Ir al inicio']);
});

// Comprueba que un 404 de la API muestra la página 404 con la navegación del usuario.
test('un 404 de la API muestra la página 404 con la navegación del usuario', function () {
    // Simula que la API no encuentra el usuario.
    Http::fake(['*' => Http::response(['message' => 'No encontrado.'], 404)]);

    // Visita el detalle de un usuario inexistente con sesión iniciada.
    $this->withSession(sesionApi())
        // Solicita el usuario 999.
        ->get(route('usuarios.mostrar', 999))
        // Verifica el código, el texto en español y el saludo del layout.
        ->assertNotFound()->assertSee(['Página no encontrada', 'Hola, Ana Pérez']);
});

// Comprueba que un formulario caducado muestra la página 419 en español.
test('un formulario caducado muestra la página 419 en español', function () {
    // Visita la ruta que responde 419.
    $this->get('/prueba/error-419')->assertStatus(419)->assertSee('Página expirada');
});

// Comprueba que un error inesperado muestra la página 500 en español sin detalles internos.
test('un error inesperado muestra la página 500 en español sin detalles internos', function () {
    // Desactiva el modo de depuración como en producción.
    config(['app.debug' => false]);

    // Visita la ruta que lanza el error.
    $this->get('/prueba/error-500')
        // Verifica el código y el texto en español.
        ->assertStatus(500)->assertSee('Error del servidor')
        // Verifica que no se muestra el mensaje interno de la excepción.
        ->assertDontSee('Fallo de prueba');
});

// Comprueba que la API caída sin página anterior muestra la página 503 en español.
test('la API caída sin página anterior muestra la página 503 en español', function () {
    // Visita la ruta directamente, sin página anterior a la que volver.
    $this->get('/prueba/error-503')
        // Verifica el código y los textos en español.
        ->assertStatus(503)->assertSee(['Servicio no disponible', 'No se pudo conectar con el servidor. Intente nuevamente.']);
});
