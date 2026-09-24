<?php

// Importa la excepción que indica que la API no está disponible.
use App\Exceptions\ApiNoDisponibleException;
// Importa la excepción que indica que el token de la API caducó.
use App\Exceptions\SesionExpiradaException;
// Importa el servicio de autenticación para leer la clave del usuario.
use App\Services\AutenticacionService;
// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la fachada de rutas para registrar rutas de prueba.
use Illuminate\Support\Facades\Route;

// Registra rutas temporales que lanzan las excepciones antes de cada prueba.
beforeEach(function () {
    // Registra una ruta GET que simula un token caducado.
    Route::middleware('web')->get('/prueba/sesion-expirada', fn () => throw new SesionExpiradaException);

    // Registra una ruta GET que simula que la API no responde.
    Route::middleware('web')->get('/prueba/api-caida', fn () => throw new ApiNoDisponibleException);

    // Registra una ruta POST que simula que la API no responde al enviar un formulario.
    Route::middleware('web')->post('/prueba/api-caida', fn () => throw new ApiNoDisponibleException);
});

// Comprueba que la sesión expirada elimina el token y redirige al inicio de sesión.
test('SesionExpiradaException elimina el token y redirige al inicio de sesión', function () {
    // Prepara una sesión con token y usuario.
    $this->withSession([ClienteApi::CLAVE_TOKEN => 'token-caducado', AutenticacionService::CLAVE_USUARIO => ['id' => 1]])
        // Visita la ruta que lanza la excepción.
        ->get('/prueba/sesion-expirada')
        // Verifica la redirección al formulario de inicio de sesión.
        ->assertRedirect('/iniciar-sesion')
        // Verifica el mensaje de sesión expirada.
        ->assertSessionHas('error', 'Su sesión ha expirado.')
        // Verifica que el token se eliminó.
        ->assertSessionMissing(ClienteApi::CLAVE_TOKEN)
        // Verifica que los datos del usuario se eliminaron.
        ->assertSessionMissing(AutenticacionService::CLAVE_USUARIO);
});

// Comprueba que la API caída vuelve al formulario con el mensaje y sin contraseñas.
test('ApiNoDisponibleException vuelve atrás con el mensaje y los datos sin contraseña', function () {
    // Envía el formulario desde una página anterior conocida.
    $this->from('/formulario-anterior')
        // Envía datos que incluyen una contraseña.
        ->post('/prueba/api-caida', ['correo_electronico' => 'ana@correo.com', 'contrasena' => 'secreta123'])
        // Verifica que vuelve a la página anterior.
        ->assertRedirect('/formulario-anterior')
        // Verifica el mensaje de servidor no disponible.
        ->assertSessionHas('error', 'No se pudo conectar con el servidor. Intente nuevamente.')
        // Verifica que se conservó el correo escrito.
        ->assertSessionHasInput('correo_electronico', 'ana@correo.com');

    // Verifica que la contraseña no se guardó en la sesión.
    expect(session()->getOldInput('contrasena'))->toBeNull();
});

// Comprueba que la carga fallida de una página muestra un 503 en lugar de redirigir, evitando bucles.
test('ApiNoDisponibleException muestra un 503 al cargar una página aunque haya página anterior', function () {
    // Simula que se llega desde otra página que también depende de la API.
    $this->from('/usuarios?page=2')
        // Visita la ruta que lanza la excepción.
        ->get('/prueba/api-caida')
        // Verifica que se responde con el código 503 en lugar de redirigir.
        ->assertStatus(503);
});
