<?php

// Importa el modelo de usuarios.
use App\Models\User;

// Verifica que un usuario con credenciales válidas obtiene un token de acceso.
test('el inicio de sesión con credenciales correctas devuelve un token', function () {
    // Crea un usuario de prueba cuya contraseña predeterminada es "password".
    $usuario = User::factory()->create();

    // Envía la solicitud de inicio de sesión con credenciales válidas.
    $respuesta = $this->postJson(route('api.iniciar-sesion'), [
        // Correo del usuario creado.
        'correo_electronico' => $usuario->email,
        // Contraseña correcta definida por la factory.
        'contrasena' => 'password',
    ]);

    // Comprueba el código 200 y la estructura del cuerpo de la respuesta.
    $respuesta->assertOk()
        // Comprueba que la respuesta contiene el token y los datos del usuario.
        ->assertJsonStructure(['mensaje', 'token', 'tipo_token', 'usuario' => ['id', 'nombre', 'correo_electronico']])
        // Comprueba que el usuario devuelto es el que inició sesión.
        ->assertJsonPath('usuario.id', $usuario->id);

    // Comprueba que se almacenó un token de acceso para el usuario.
    expect($usuario->tokens()->count())->toBe(1);
});

// Verifica que el registro acepta los campos en español y los guarda en las columnas correctas.
test('el registro con campos en español crea el usuario y devuelve un token', function () {
    // Envía la solicitud de registro con los nombres de campo del contrato de la API.
    $respuesta = $this->postJson(route('api.registro'), [
        // Nombre del nuevo usuario.
        'nombre' => 'Carlos Pérez',
        // Correo del nuevo usuario.
        'correo_electronico' => 'carlos@ejemplo.com',
        // Contraseña del nuevo usuario.
        'contrasena' => 'password',
        // Confirmación de la contraseña.
        'contrasena_confirmation' => 'password',
        // Cédula del nuevo usuario.
        'cedula' => '1712345678',
    ]);

    // Comprueba el código 201 y que se devolvió el token y el correo registrado.
    $respuesta->assertCreated()
        // Comprueba que la respuesta incluye el token de acceso.
        ->assertJsonStructure(['token'])
        // Comprueba que el correo devuelto usa el mismo nombre de campo que la solicitud.
        ->assertJsonPath('usuario.correo_electronico', 'carlos@ejemplo.com');

    // Comprueba que los campos en español se guardaron en las columnas internas.
    $this->assertDatabaseHas('users', ['name' => 'Carlos Pérez', 'email' => 'carlos@ejemplo.com', 'cedula' => '1712345678']);
});

// Verifica que el límite de intentos se aplica por correo y no bloquea a otros usuarios de la misma IP.
test('el límite de inicio de sesión bloquea por correo sin afectar a otros correos', function () {
    // Crea el usuario que sufrirá los intentos fallidos.
    $usuarioAtacado = User::factory()->create();
    // Crea otro usuario que inicia sesión desde la misma IP (el servidor BFF).
    $otroUsuario = User::factory()->create();

    // Repite cinco intentos fallidos, el máximo permitido por minuto.
    foreach (range(1, 5) as $intento) {
        // Envía credenciales incorrectas y comprueba el error de validación.
        $this->postJson(route('api.iniciar-sesion'), [
            // Correo del usuario atacado.
            'correo_electronico' => $usuarioAtacado->email,
            // Contraseña incorrecta.
            'contrasena' => 'incorrecta',
        ])->assertUnprocessable();
    }

    // Comprueba que el sexto intento contra el mismo correo se bloquea con el código 429.
    $this->postJson(route('api.iniciar-sesion'), [
        // Correo del usuario atacado.
        'correo_electronico' => $usuarioAtacado->email,
        // Contraseña correcta, que igualmente debe bloquearse.
        'contrasena' => 'password',
    ])->assertTooManyRequests();

    // Comprueba que otro correo desde la misma IP puede iniciar sesión con normalidad.
    $this->postJson(route('api.iniciar-sesion'), [
        // Correo del otro usuario.
        'correo_electronico' => $otroUsuario->email,
        // Contraseña correcta del otro usuario.
        'contrasena' => 'password',
    ])->assertOk();
});

// Verifica que el token de acceso deja de ser válido al superar los minutos de SANCTUM_EXPIRATION.
test('el token caduca según la configuración de expiración', function (int $minutosTranscurridos, int $estadoEsperado) {
    // Fija la caducidad en 120 minutos para que la prueba no dependa del archivo .env.
    config(['sanctum.expiration' => 120]);

    // Crea el usuario propietario del token.
    $usuario = User::factory()->create();

    // Genera un token de acceso personal para el usuario.
    $token = $usuario->createToken('token_api')->plainTextToken;

    // Avanza el reloj de la aplicación los minutos indicados por el caso.
    $this->travel($minutosTranscurridos)->minutes();

    // Consulta el perfil con el token y comprueba el código HTTP esperado.
    $this->withToken($token)->getJson(route('api.mi-perfil'))->assertStatus($estadoEsperado);
})->with([
    // Antes de caducar, el token sigue siendo válido.
    'token vigente a los 119 minutos' => [119, 200],
    // Después de caducar, la API responde 401.
    'token caducado a los 121 minutos' => [121, 401],
]);

// Verifica que las rutas protegidas rechazan las solicitudes sin token.
test('el acceso sin token a rutas protegidas devuelve 401', function (string $nombreRuta) {
    // Envía la solicitud sin cabecera Authorization y comprueba el código 401.
    $this->getJson(route($nombreRuta))->assertUnauthorized();
})->with([
    // Ruta del perfil del usuario autenticado.
    'mi perfil' => 'api.mi-perfil',
    // Ruta del listado de usuarios del CRUD.
    'listado de usuarios' => 'api.usuarios.index',
]);
