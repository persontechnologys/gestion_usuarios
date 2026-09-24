<?php

// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

/**
 * Devuelve datos válidos del formulario de creación, con la posibilidad de sobrescribir algunos.
 *
 * @param  array<string, mixed>  $cambios
 * @return array<string, mixed>
 */
function datosNuevoUsuario(array $cambios = []): array
{
    // Combina los datos válidos con los cambios indicados.
    return array_merge([
        'nombre' => 'Eva Torres',
        'correo_electronico' => 'eva@correo.com',
        'cedula' => '1102030405',
        'telefono' => null,
        'direccion' => null,
        'contrasena' => 'secreta123',
        'contrasena_confirmation' => 'secreta123',
    ], $cambios);
}

// Comprueba que el formulario de creación se muestra vacío.
test('muestra el formulario de creación', function () {
    // Visita el formulario con sesión iniciada.
    $respuesta = $this->withSession(sesionApi())
        // Solicita la URL en español del formulario.
        ->get('/usuarios/crear');

    // Verifica el título y el botón.
    $respuesta->assertOk()->assertSee(['Nuevo usuario', 'Crear usuario']);

    // Verifica que el campo de la contraseña es obligatorio al crear.
    expect($respuesta->getContent())->toMatch('/<input[^>]*name="contrasena"[^>]*required/');
});

// Comprueba que crear un usuario redirige con un mensaje de éxito.
test('crear un usuario redirige al listado con un mensaje de éxito', function () {
    // Simula la respuesta 201 de la creación.
    Http::fake(['api.prueba/api/usuarios' => Http::response(['data' => usuarioApi(['id' => 9, 'nombre' => 'Eva Torres'])], 201)]);

    // Envía el formulario con sesión iniciada.
    $this->withSession(sesionApi())
        // Envía los datos del usuario nuevo.
        ->post(route('usuarios.guardar'), datosNuevoUsuario())
        // Verifica la redirección al listado.
        ->assertRedirect(route('usuarios.indice'))
        // Verifica el mensaje de éxito.
        ->assertSessionHas('exito', 'El usuario Eva Torres se creó correctamente.');

    // Verifica que la API recibió los datos, incluida la confirmación de la contraseña.
    Http::assertSent(fn (Request $peticion): bool => $peticion->method() === 'POST' && $peticion->data() == datosNuevoUsuario());
});

// Comprueba que un 422 de la API vuelve al formulario con errores y valores anteriores.
test('un 422 al crear vuelve con los errores y los valores anteriores', function () {
    // Simula el rechazo de la cédula por estar registrada.
    Http::fake(['*' => Http::response([
        'message' => 'La cédula ya se encuentra registrada.',
        'errors' => ['cedula' => ['El cédula ya se encuentra registrado.']],
    ], 422)]);

    // Envía el formulario desde la página de creación.
    $this->withSession(sesionApi())
        // Indica la página de origen para la redirección de vuelta.
        ->from(route('usuarios.crear'))
        // Envía datos que la API rechazará.
        ->post(route('usuarios.guardar'), datosNuevoUsuario())
        // Verifica que vuelve al formulario.
        ->assertRedirect(route('usuarios.crear'))
        // Verifica el error bajo el campo de la cédula.
        ->assertSessionHasErrors(['cedula' => 'El cédula ya se encuentra registrado.'])
        // Verifica que se conservó el nombre escrito.
        ->assertSessionHasInput('nombre', 'Eva Torres');

    // Verifica que el formulario muestra el error y el valor anterior, sin la contraseña.
    $this->get(route('usuarios.crear'))
        // Verifica el mensaje de error visible.
        ->assertSee('El cédula ya se encuentra registrado.')
        // Verifica que el nombre escrito aparece en el campo.
        ->assertSee('value="Eva Torres"', false)
        // Verifica que la contraseña no aparece en la página.
        ->assertDontSee('secreta123');
});

// Comprueba que la validación local evita llamar a la API.
test('la validación local rechaza datos incompletos sin llamar a la API', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Envía el formulario vacío con sesión iniciada.
    $this->withSession(sesionApi())
        // Envía campos obligatorios vacíos.
        ->post(route('usuarios.guardar'), datosNuevoUsuario(['nombre' => '', 'cedula' => '', 'contrasena' => '', 'contrasena_confirmation' => '']))
        // Verifica los mensajes en español.
        ->assertSessionHasErrors([
            'nombre' => 'El campo nombre es obligatorio.',
            'cedula' => 'El campo cédula es obligatorio.',
            'contrasena' => 'El campo contraseña es obligatorio.',
        ]);

    // Verifica que no se envió ninguna petición a la API.
    Http::assertNothingSent();
});
