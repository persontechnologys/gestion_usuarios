<?php

// Importa el servicio de autenticación para leer la clave del usuario.
use App\Services\AutenticacionService;
// Importa el cliente de la API para leer la clave del token.
use App\Services\ClienteApi;
// Importa la petición registrada por Http::fake() para inspeccionarla.
use Illuminate\Http\Client\Request;
// Importa la fachada del cliente HTTP para simular la API.
use Illuminate\Support\Facades\Http;

// Comprueba que el formulario de inicio de sesión se muestra con sus campos.
test('muestra el formulario de inicio de sesión', function () {
    // Visita el formulario de inicio de sesión.
    $this->get(route('sesion.formulario'))
        // Verifica que la página carga correctamente.
        ->assertOk()
        // Verifica los textos y campos principales.
        ->assertSee('Iniciar sesión')
        // Verifica que existe el campo del correo con el nombre de la API.
        ->assertSee('name="correo_electronico"', false)
        // Verifica que existe el campo de la contraseña con el nombre de la API.
        ->assertSee('name="contrasena"', false);
});

// Comprueba que un inicio de sesión correcto guarda el token y redirige al listado.
test('un inicio de sesión correcto redirige a /usuarios y guarda el token', function () {
    // Simula la respuesta correcta de la API.
    Http::fake(['api.prueba/api/iniciar-sesion' => Http::response([
        'mensaje' => 'Inicio de sesión exitoso.',
        'usuario' => ['id' => 3, 'nombre' => 'Ana Pérez', 'correo_electronico' => 'ana@correo.com'],
        'token' => '3|token-de-prueba',
        'tipo_token' => 'Bearer',
    ])]);

    // Envía las credenciales del formulario.
    $this->post(route('sesion.iniciar'), ['correo_electronico' => 'ana@correo.com', 'contrasena' => 'secreta123'])
        // Verifica la redirección al listado de usuarios.
        ->assertRedirect('/usuarios')
        // Verifica el mensaje de bienvenida.
        ->assertSessionHas('exito', 'Bienvenido, Ana Pérez.')
        // Verifica que el token quedó en la sesión del servidor.
        ->assertSessionHas(ClienteApi::CLAVE_TOKEN, '3|token-de-prueba')
        // Verifica que se guardó el nombre del usuario para el layout.
        ->assertSessionHas(AutenticacionService::CLAVE_USUARIO.'.nombre', 'Ana Pérez');

    // Verifica que se enviaron a la API exactamente las credenciales escritas.
    Http::assertSent(fn (Request $peticion): bool => $peticion->data() === ['correo_electronico' => 'ana@correo.com', 'contrasena' => 'secreta123']);
});

// Comprueba que las credenciales incorrectas muestran el error en el formulario.
test('las credenciales incorrectas (422) muestran el error en el formulario', function () {
    // Simula el rechazo de las credenciales por parte de la API.
    Http::fake(['*' => Http::response([
        'message' => 'Las credenciales no son válidas.',
        'errors' => ['correo_electronico' => ['Las credenciales proporcionadas no son válidas.']],
    ], 422)]);

    // Envía el formulario desde la página de inicio de sesión.
    $this->from(route('sesion.formulario'))
        // Envía credenciales incorrectas.
        ->post(route('sesion.iniciar'), ['correo_electronico' => 'ana@correo.com', 'contrasena' => 'incorrecta'])
        // Verifica que vuelve al formulario.
        ->assertRedirect(route('sesion.formulario'))
        // Verifica el error bajo el campo del correo.
        ->assertSessionHasErrors(['correo_electronico' => 'Las credenciales proporcionadas no son válidas.'])
        // Verifica que no se guardó ningún token.
        ->assertSessionMissing(ClienteApi::CLAVE_TOKEN);

    // Verifica que el formulario muestra el error y conserva el correo, pero no la contraseña.
    $this->get(route('sesion.formulario'))
        // Verifica el mensaje de error visible.
        ->assertSee('Las credenciales proporcionadas no son válidas.')
        // Verifica que el correo escrito se conserva.
        ->assertSee('value="ana@correo.com"', false)
        // Verifica que la contraseña no aparece en la página.
        ->assertDontSee('incorrecta');
});

// Comprueba que el exceso de intentos muestra el mensaje de la API.
test('el límite de intentos (429) muestra el mensaje de la API en el formulario', function () {
    // Simula la respuesta de límite de intentos.
    Http::fake(['*' => Http::response(['message' => 'Demasiados intentos de inicio de sesión. Intente nuevamente en un minuto.'], 429)]);

    // Envía el formulario desde la página de inicio de sesión.
    $this->from(route('sesion.formulario'))
        // Envía un intento más de inicio de sesión.
        ->post(route('sesion.iniciar'), ['correo_electronico' => 'ana@correo.com', 'contrasena' => 'incorrecta'])
        // Verifica que vuelve al formulario con el mensaje en el campo del correo.
        ->assertSessionHasErrors(['correo_electronico' => 'Demasiados intentos de inicio de sesión. Intente nuevamente en un minuto.']);
});

// Comprueba que la validación local evita llamar a la API con datos incompletos.
test('valida los campos obligatorios sin llamar a la API', function () {
    // Simula la API para detectar cualquier llamada.
    Http::fake();

    // Envía el formulario vacío.
    $this->from(route('sesion.formulario'))
        // Envía campos vacíos.
        ->post(route('sesion.iniciar'), ['correo_electronico' => '', 'contrasena' => ''])
        // Verifica los mensajes en español de cada campo.
        ->assertSessionHasErrors([
            'correo_electronico' => 'El correo electrónico es obligatorio.',
            'contrasena' => 'La contraseña es obligatoria.',
        ]);

    // Verifica que no se envió ninguna petición a la API.
    Http::assertNothingSent();
});

// Comprueba que la API caída muestra el mensaje de servidor no disponible.
test('muestra el mensaje de servidor no disponible si la API no responde', function () {
    // Simula un error interno de la API.
    Http::fake(['*' => Http::response([], 500)]);

    // Envía el formulario desde la página de inicio de sesión.
    $this->from(route('sesion.formulario'))
        // Envía credenciales válidas.
        ->post(route('sesion.iniciar'), ['correo_electronico' => 'ana@correo.com', 'contrasena' => 'secreta123'])
        // Verifica que vuelve al formulario.
        ->assertRedirect(route('sesion.formulario'))
        // Verifica el mensaje de error.
        ->assertSessionHas('error', 'No se pudo conectar con el servidor. Intente nuevamente.');
});
