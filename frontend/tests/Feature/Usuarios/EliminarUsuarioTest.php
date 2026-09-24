<?php

// Importa el servicio de autenticación para leer la clave del usuario.
use App\Services\AutenticacionService;
// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

// Comprueba que eliminar otro usuario redirige al listado con un mensaje de éxito.
test('eliminar otro usuario redirige al listado con un mensaje de éxito', function () {
    // Simula la respuesta de la eliminación.
    Http::fake(['api.prueba/api/usuarios/2' => Http::response(['mensaje' => 'Usuario eliminado correctamente.'])]);

    // Envía la eliminación con sesión iniciada.
    $this->withSession(sesionApi())
        // Envía el DELETE del usuario 2.
        ->delete(route('usuarios.eliminar', 2))
        // Verifica la redirección al listado.
        ->assertRedirect(route('usuarios.indice'))
        // Verifica el mensaje de éxito.
        ->assertSessionHas('exito', 'El usuario se eliminó correctamente.')
        // Verifica que la sesión sigue activa.
        ->assertSessionHas(ClienteApi::CLAVE_TOKEN);

    // Verifica que se envió un DELETE a la API.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'DELETE' && $peticion->url() === 'http://api.prueba/api/usuarios/2');
});

// Comprueba que eliminar la propia cuenta cierra la sesión local.
test('eliminar al usuario autenticado cierra la sesión local y redirige al inicio de sesión', function () {
    // Simula la respuesta de la eliminación del usuario 1.
    Http::fake(['api.prueba/api/usuarios/1' => Http::response(['mensaje' => 'Usuario eliminado correctamente.'])]);

    // Envía la eliminación de la propia cuenta.
    $this->withSession(sesionApi())
        // Envía el DELETE del usuario 1, que es el autenticado.
        ->delete(route('usuarios.eliminar', 1))
        // Verifica la redirección al inicio de sesión.
        ->assertRedirect(route('sesion.formulario'))
        // Verifica el mensaje informativo.
        ->assertSessionHas('exito', 'Su cuenta se eliminó correctamente y la sesión se cerró.')
        // Verifica que el token se eliminó.
        ->assertSessionMissing(ClienteApi::CLAVE_TOKEN)
        // Verifica que los datos del usuario se eliminaron.
        ->assertSessionMissing(AutenticacionService::CLAVE_USUARIO);

    // Verifica que solo se llamó a la API para eliminar, no para cerrar sesión.
    Http::assertSentCount(1);
});
