<?php

// Comprueba que la aplicación responde y que la raíz redirige al inicio de sesión.
test('la aplicación responde y la raíz redirige al inicio de sesión', function () {
    // Solicita la página raíz sin sesión.
    $response = $this->get('/');

    // Verifica la redirección al formulario de inicio de sesión.
    $response->assertRedirect(route('sesion.formulario'));
});
