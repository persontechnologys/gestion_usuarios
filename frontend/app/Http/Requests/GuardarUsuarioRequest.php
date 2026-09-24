<?php

namespace App\Http\Requests;

/**
 * Valida los datos del formulario de creación de usuarios antes de enviarlos a la API.
 *
 * La API aplica al crear un usuario las mismas reglas que al registrarse, por lo
 * que hereda las reglas, los mensajes y los nombres de campo de RegistroRequest.
 */
class GuardarUsuarioRequest extends RegistroRequest {}
