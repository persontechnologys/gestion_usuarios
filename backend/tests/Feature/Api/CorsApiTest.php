<?php

// Verifica que la petición preflight desde un origen permitido recibe la cabecera CORS con ese origen.
test('la petición preflight desde un origen permitido recibe Access-Control-Allow-Origin', function () {
    // Envía una petición OPTIONS de verificación previa a la ruta de usuarios desde el frontend local.
    $respuesta = $this->call('OPTIONS', '/api/usuarios', [], [], [], [
        // Indica el origen del frontend que realiza la petición.
        'HTTP_ORIGIN' => 'http://127.0.0.1:5500',
        // Indica el método que el navegador pretende utilizar después de la verificación.
        'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
        // Indica las cabeceras que el navegador pretende enviar, incluido el token Bearer.
        'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'authorization,content-type',
    ]);

    // Comprueba que la respuesta autoriza exactamente el origen solicitado.
    $respuesta->assertHeader('Access-Control-Allow-Origin', 'http://127.0.0.1:5500');
});

// Verifica que la petición preflight desde un origen no permitido no recibe la cabecera CORS.
test('la petición preflight desde un origen no permitido no recibe Access-Control-Allow-Origin', function () {
    // Envía una petición OPTIONS de verificación previa desde un origen que no está en la lista.
    $respuesta = $this->call('OPTIONS', '/api/usuarios', [], [], [], [
        // Indica un origen ajeno a los frontends autorizados.
        'HTTP_ORIGIN' => 'http://sitio-no-autorizado.com',
        // Indica el método que el navegador pretende utilizar después de la verificación.
        'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
    ]);

    // Comprueba que la respuesta no incluye la cabecera que autorizaría el origen.
    $respuesta->assertHeaderMissing('Access-Control-Allow-Origin');
});
