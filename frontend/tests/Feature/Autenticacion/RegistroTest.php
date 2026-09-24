<?php

// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

/**
 * Devuelve datos de registro válidos, con la posibilidad de sobrescribir algunos.
 *
 * @param  array<string, mixed>  $cambios
 * @return array<string, mixed>
 */
function datosRegistro(array $cambios = []): array
{
    // Combina los datos válidos con los cambios indicados.
    return array_merge([
        'nombre' => 'Luis Gómez',
        'correo_electronico' => 'luis@correo.com',
        'cedula' => '0102030405',
        'telefono' => '0991234567',
        'direccion' => 'Av. Principal 123',
        'contrasena' => 'secreta123',
        'contrasena_confirmation' => 'secreta123',
    ], $cambios);
}

// Comprueba que el formulario de registro se muestra con todos sus campos.
test('muestra el formulario de registro', function () {
    // Visita el formulario de registro.
    $this->get(route('registro.formulario'))
        // Verifica que la página carga correctamente.
        ->assertOk()
        // Verifica los campos con los nombres de la API.
        ->assertSee(['name="nombre"', 'name="cedula"', 'name="telefono"', 'name="direccion"', 'name="contrasena_confirmation"'], false);
});

// Comprueba que un registro correcto envía los datos, guarda el token y redirige.
test('un registro correcto guarda el token y redirige a /usuarios', function () {
    // Simula la respuesta 201 del registro.
    Http::fake(['api.prueba/api/registro' => Http::response([
        'mensaje' => 'Usuario registrado.',
        'usuario' => ['id' => 5, 'nombre' => 'Luis Gómez', 'correo_electronico' => 'luis@correo.com'],
        'token' => '5|token-de-prueba',
        'tipo_token' => 'Bearer',
    ], 201)]);

    // Envía el formulario con datos válidos.
    $this->post(route('registro.guardar'), datosRegistro())
        // Verifica la redirección al listado de usuarios.
        ->assertRedirect('/usuarios')
        // Verifica el mensaje de bienvenida.
        ->assertSessionHas('exito')
        // Verifica que el token quedó en la sesión.
        ->assertSessionHas(ClienteApi::CLAVE_TOKEN, '5|token-de-prueba');

    // Verifica que la API recibió todos los campos, incluida la confirmación de la contraseña.
    Http::assertSent(fn (Request $peticion): bool => $peticion->data() == datosRegistro());
});

// Comprueba que la validación local rechaza una cédula incorrecta y una contraseña corta.
test('valida la cédula y la contraseña sin llamar a la API', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Envía el formulario con datos inválidos.
    $this->from(route('registro.formulario'))
        // Envía una cédula de 5 dígitos y una contraseña de 3 caracteres.
        ->post(route('registro.guardar'), datosRegistro(['cedula' => '12345', 'contrasena' => 'abc', 'contrasena_confirmation' => 'abc']))
        // Verifica los mensajes en español.
        ->assertSessionHasErrors([
            'cedula' => 'El campo cédula debe tener 10 dígitos.',
            'contrasena' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

    // Verifica que no se envió ninguna petición a la API.
    Http::assertNothingSent();
});

// Comprueba que un correo ya registrado (422 de la API) vuelve con el error y los datos.
test('un correo duplicado (422) vuelve al formulario con el error y los valores anteriores', function () {
    // Simula el rechazo del correo por estar registrado.
    Http::fake(['*' => Http::response([
        'message' => 'El correo electrónico ya se encuentra registrado.',
        'errors' => ['correo_electronico' => ['El correo electrónico ya se encuentra registrado.']],
    ], 422)]);

    // Envía el formulario desde la página de registro.
    $this->from(route('registro.formulario'))
        // Envía datos válidos que la API rechazará.
        ->post(route('registro.guardar'), datosRegistro())
        // Verifica que vuelve al formulario.
        ->assertRedirect(route('registro.formulario'))
        // Verifica el error de la API bajo el campo del correo.
        ->assertSessionHasErrors(['correo_electronico' => 'El correo electrónico ya se encuentra registrado.'])
        // Verifica que se conservó el nombre escrito.
        ->assertSessionHasInput('nombre', 'Luis Gómez');

    // Verifica que las contraseñas no se guardaron en la sesión.
    expect(session()->getOldInput('contrasena'))->toBeNull()
        // Verifica que la confirmación tampoco se guardó.
        ->and(session()->getOldInput('contrasena_confirmation'))->toBeNull();
});
