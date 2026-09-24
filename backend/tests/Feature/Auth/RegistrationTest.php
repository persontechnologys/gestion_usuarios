<?php

// Importa el modelo de usuarios para crear el usuario con cédula duplicada.
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        // Envía una cédula válida de diez dígitos, requerida por el registro.
        'cedula' => '1712345678',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    // Comprueba que la cédula se guardó en el usuario registrado.
    $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'cedula' => '1712345678']);
});

// Verifica que el registro se rechaza cuando la cédula falta, es inválida o ya existe.
test('el registro falla con una cédula inválida', function (?string $cedula) {
    // Crea un usuario existente cuya cédula se usará para el caso de duplicado.
    User::factory()->create(['cedula' => '0912345678']);

    // Envía el formulario de registro con la cédula del caso evaluado.
    $response = $this->post('/register', [
        // Nombre del usuario de prueba.
        'name' => 'Usuario Prueba',
        // Correo del usuario de prueba.
        'email' => 'prueba@example.com',
        // Contraseña del usuario de prueba.
        'password' => 'password',
        // Confirmación de la contraseña.
        'password_confirmation' => 'password',
        // Cédula del caso evaluado.
        'cedula' => $cedula,
    ]);

    // Comprueba que la validación devolvió un error en el campo de cédula.
    $response->assertSessionHasErrors('cedula');

    // Comprueba que no se inició sesión con el registro rechazado.
    $this->assertGuest();
})->with([
    // Caso sin cédula.
    'cédula vacía' => [null],
    // Caso con menos de diez dígitos.
    'cédula corta' => ['12345'],
    // Caso con una cédula ya registrada.
    'cédula duplicada' => ['0912345678'],
]);
