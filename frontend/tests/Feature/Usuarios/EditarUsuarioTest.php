<?php

// Importa el servicio de autenticación para leer la clave del usuario.
use App\Services\AutenticacionService;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

/**
 * Devuelve datos válidos del formulario de edición, con la posibilidad de sobrescribir algunos.
 *
 * @param  array<string, mixed>  $cambios
 * @return array<string, mixed>
 */
function datosEdicion(array $cambios = []): array
{
    // Combina los datos válidos con los cambios indicados.
    return array_merge([
        'nombre' => 'Luis Gómez Ruiz',
        'correo_electronico' => 'luis@correo.com',
        'cedula' => '0102030405',
        'telefono' => '0991234567',
        'direccion' => 'Av. Principal 123',
        'contrasena' => '',
        'contrasena_confirmation' => '',
    ], $cambios);
}

// Comprueba que el formulario de edición muestra los datos actuales.
test('el formulario de edición muestra los datos actuales del usuario', function () {
    // Simula la respuesta del usuario que se edita.
    Http::fake(['api.prueba/api/usuarios/2' => Http::response(['data' => usuarioApi()])]);

    // Visita la URL en español del formulario de edición.
    $respuesta = $this->withSession(sesionApi())
        // Solicita el formulario del usuario 2.
        ->get('/usuarios/2/editar');

    // Verifica que el campo de la contraseña no es obligatorio al editar.
    expect($respuesta->getContent())->not->toMatch('/<input[^>]*name="contrasena"[^>]*required/');

    // Verifica el contenido del formulario.
    $respuesta
        // Verifica que la página carga correctamente.
        ->assertOk()
        // Verifica que los campos contienen los valores actuales.
        ->assertSee(['value="Luis Gómez"', 'value="luis@correo.com"', 'value="0102030405"', 'name="_method" value="PUT"'], false)
        // Verifica la indicación de que la contraseña es opcional.
        ->assertSee('Déjela en blanco para conservar la contraseña actual.');
});

// Comprueba que actualizar sin contraseña no la envía a la API.
test('actualizar sin contraseña no la envía a la API y redirige con éxito', function () {
    // Simula la respuesta de la actualización.
    Http::fake(['api.prueba/api/usuarios/2' => Http::response(['data' => usuarioApi(['nombre' => 'Luis Gómez Ruiz'])])]);

    // Envía el formulario con la contraseña en blanco.
    $this->withSession(sesionApi())
        // Envía los datos modificados.
        ->put(route('usuarios.actualizar', 2), datosEdicion())
        // Verifica la redirección al detalle.
        ->assertRedirect(route('usuarios.mostrar', 2))
        // Verifica el mensaje de éxito.
        ->assertSessionHas('exito', 'Los datos del usuario se actualizaron correctamente.');

    // Verifica que se envió un PUT sin los campos de contraseña.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'PUT'
        && $peticion['nombre'] === 'Luis Gómez Ruiz'
        && ! array_key_exists('contrasena', $peticion->data())
        && ! array_key_exists('contrasena_confirmation', $peticion->data()));
});

// Comprueba que actualizar con contraseña la envía junto con su confirmación.
test('actualizar con contraseña la envía con su confirmación', function () {
    // Simula la respuesta de la actualización.
    Http::fake(['api.prueba/api/usuarios/2' => Http::response(['data' => usuarioApi()])]);

    // Envía el formulario con una contraseña nueva.
    $this->withSession(sesionApi())
        // Envía los datos con la contraseña nueva.
        ->put(route('usuarios.actualizar', 2), datosEdicion(['contrasena' => 'nueva12345', 'contrasena_confirmation' => 'nueva12345']))
        // Verifica la redirección al detalle.
        ->assertRedirect(route('usuarios.mostrar', 2));

    // Verifica que la API recibió la contraseña y su confirmación.
    Http::assertSent(fn (Request $peticion): bool => $peticion['contrasena'] === 'nueva12345' && $peticion['contrasena_confirmation'] === 'nueva12345');
});

// Comprueba que la confirmación distinta se rechaza sin llamar a la API.
test('una confirmación de contraseña distinta se rechaza sin llamar a la API', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Envía el formulario con confirmación incorrecta.
    $this->withSession(sesionApi())
        // Envía contraseñas que no coinciden.
        ->put(route('usuarios.actualizar', 2), datosEdicion(['contrasena' => 'nueva12345', 'contrasena_confirmation' => 'otra12345']))
        // Verifica el mensaje en español.
        ->assertSessionHasErrors(['contrasena' => 'La confirmación de la contraseña no coincide.']);

    // Verifica que no se envió ninguna petición a la API.
    Http::assertNothingSent();
});

// Comprueba que editar los propios datos actualiza el nombre mostrado en el layout.
test('editar al usuario autenticado actualiza su nombre en la sesión', function () {
    // Simula la respuesta de la actualización del usuario 1.
    Http::fake(['api.prueba/api/usuarios/1' => Http::response(['data' => usuarioApi(['id' => 1, 'nombre' => 'Ana María Pérez', 'correo_electronico' => 'ana@correo.com'])])]);

    // Envía el formulario del propio usuario.
    $this->withSession(sesionApi())
        // Envía el nombre nuevo.
        ->put(route('usuarios.actualizar', 1), datosEdicion(['nombre' => 'Ana María Pérez', 'correo_electronico' => 'ana@correo.com']))
        // Verifica que la sesión guarda el nombre nuevo.
        ->assertSessionHas(AutenticacionService::CLAVE_USUARIO.'.nombre', 'Ana María Pérez');
});
