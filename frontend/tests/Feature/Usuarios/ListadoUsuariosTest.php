<?php

// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

/**
 * Construye una respuesta paginada de la API con los usuarios y la página indicados.
 *
 * @param  array<int, array<string, mixed>>  $usuarios
 * @return array<string, mixed>
 */
function respuestaListado(array $usuarios, int $paginaActual = 1, int $ultimaPagina = 1, int $total = 0): array
{
    // Devuelve el cuerpo con data, links y meta como lo envía la API.
    return [
        'data' => $usuarios,
        'links' => [],
        'meta' => ['current_page' => $paginaActual, 'from' => 1, 'last_page' => $ultimaPagina, 'per_page' => 15, 'to' => count($usuarios), 'total' => $total ?: count($usuarios)],
    ];
}

// Comprueba que el listado muestra los usuarios, la paginación y el saludo del layout.
test('el listado muestra los usuarios y la paginación', function () {
    // Simula la segunda de tres páginas del listado.
    Http::fake(['api.prueba/api/usuarios*' => Http::response(respuestaListado([usuarioApi()], 2, 3, 31))]);

    // Visita el listado de la segunda página con sesión iniciada.
    $this->withSession(sesionApi())
        // Solicita la página 2.
        ->get(route('usuarios.indice', ['page' => 2]))
        // Verifica que la página carga correctamente.
        ->assertOk()
        // Verifica los datos del usuario y la fecha convertida de UTC (10:30) a la hora de Ecuador (05:30).
        ->assertSee(['Luis Gómez', 'luis@correo.com', '0102030405', '0991234567', '01/09/2026 05:30'])
        // Verifica el indicador de página.
        ->assertSee('Página 2 de 3')
        // Verifica el enlace a la página anterior.
        ->assertSee(route('usuarios.indice', ['page' => 1]), false)
        // Verifica el enlace a la página siguiente.
        ->assertSee(route('usuarios.indice', ['page' => 3]), false)
        // Verifica las acciones de la fila.
        ->assertSee([route('usuarios.mostrar', 2), route('usuarios.editar', 2)], false)
        // Verifica el saludo del layout con el nombre del usuario autenticado.
        ->assertSee('Hola, Ana Pérez');

    // Verifica que se pidió a la API la página 2 con el token.
    Http::assertSent(fn (Request $peticion): bool => $peticion->url() === 'http://api.prueba/api/usuarios?page=2'
        && $peticion->hasHeader('Authorization', 'Bearer token-de-prueba'));
});

// Comprueba el estado vacío del listado.
test('el listado muestra el estado vacío cuando no hay usuarios', function () {
    // Simula un listado sin usuarios.
    Http::fake(['*' => Http::response(respuestaListado([]))]);

    // Visita el listado con sesión iniciada.
    $this->withSession(sesionApi())
        // Solicita el listado.
        ->get(route('usuarios.indice'))
        // Verifica el mensaje de estado vacío.
        ->assertOk()->assertSee('No hay usuarios registrados');
});

// Comprueba que una página inexistente redirige a la última página.
test('una página mayor que la última redirige a la última página', function () {
    // Simula la respuesta de una página vacía más allá de la última.
    Http::fake(['*' => Http::response(respuestaListado([], 9, 3, 31))]);

    // Visita la página 9 con sesión iniciada.
    $this->withSession(sesionApi())
        // Solicita una página que no existe.
        ->get(route('usuarios.indice', ['page' => 9]))
        // Verifica la redirección a la página 3.
        ->assertRedirect(route('usuarios.indice', ['page' => 3]));
});

// Comprueba que /usuarios sin sesión redirige al inicio de sesión.
test('/usuarios sin sesión redirige a /iniciar-sesion', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Visita el listado sin token.
    $this->get('/usuarios')->assertRedirect(route('sesion.formulario'));

    // Verifica que no se llamó a la API.
    Http::assertNothingSent();
});

// Comprueba que un token caducado durante la navegación lleva al inicio de sesión.
test('un 401 durante la navegación lleva al login con "Su sesión ha expirado."', function () {
    // Simula que la API rechaza el token caducado.
    Http::fake(['*' => Http::response(['message' => 'Unauthenticated.'], 401)]);

    // Visita el listado con un token que la API ya no acepta.
    $this->withSession(sesionApi())
        // Solicita el listado.
        ->get(route('usuarios.indice'))
        // Verifica la redirección al inicio de sesión.
        ->assertRedirect(route('sesion.formulario'))
        // Verifica que el token se eliminó de la sesión.
        ->assertSessionMissing(ClienteApi::CLAVE_TOKEN);

    // Verifica que el formulario de inicio de sesión muestra el mensaje.
    $this->get(route('sesion.formulario'))->assertOk()->assertSee('Su sesión ha expirado.');
});
