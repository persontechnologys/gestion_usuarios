<?php

// Importa el servicio de autenticación para leer la clave del usuario.
use App\Services\AutenticacionService;
// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la excepción que lanza el cliente HTTP cuando no logra conectarse.
use Illuminate\Http\Client\ConnectionException;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

// Comprueba que cerrar sesión revoca el token en la API y redirige al inicio de sesión.
test('cerrar sesión revoca el token y redirige al inicio de sesión', function () {
    // Simula la respuesta correcta del cierre de sesión.
    Http::fake(['api.prueba/api/cerrar-sesion' => Http::response(['mensaje' => 'Sesión cerrada.'])]);

    // Prepara una sesión con token y usuario.
    $this->withSession([ClienteApi::CLAVE_TOKEN => 'token-de-prueba', AutenticacionService::CLAVE_USUARIO => ['id' => 1, 'nombre' => 'Ana']])
        // Envía el formulario de cierre de sesión.
        ->post(route('sesion.cerrar'))
        // Verifica la redirección al formulario de inicio de sesión.
        ->assertRedirect(route('sesion.formulario'))
        // Verifica el mensaje de confirmación.
        ->assertSessionHas('exito', 'Ha cerrado sesión correctamente.')
        // Verifica que el token se eliminó.
        ->assertSessionMissing(ClienteApi::CLAVE_TOKEN)
        // Verifica que los datos del usuario se eliminaron.
        ->assertSessionMissing(AutenticacionService::CLAVE_USUARIO);

    // Verifica que se llamó a la API para revocar el token.
    Http::assertSentCount(1);
});

// Comprueba que el token se elimina aunque la API falle.
test('cerrar sesión elimina el token aunque la API falle', function () {
    // Simula que la API no responde.
    Http::fake(fn () => throw new ConnectionException('Connection refused'));

    // Prepara una sesión con token.
    $this->withSession([ClienteApi::CLAVE_TOKEN => 'token-de-prueba'])
        // Envía el formulario de cierre de sesión.
        ->post(route('sesion.cerrar'))
        // Verifica la redirección al formulario de inicio de sesión.
        ->assertRedirect(route('sesion.formulario'))
        // Verifica que el token se eliminó a pesar del fallo.
        ->assertSessionMissing(ClienteApi::CLAVE_TOKEN);
});
