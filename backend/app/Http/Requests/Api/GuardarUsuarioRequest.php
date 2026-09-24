<?php

// Declara el espacio de nombres de las solicitudes de la API.

namespace App\Http\Requests\Api;

/**
 * Valida los datos para crear un usuario desde el CRUD protegido.
 *
 * Reutiliza las reglas, mensajes y atributos del registro para no duplicar la validación.
 */
class GuardarUsuarioRequest extends RegistroUsuarioRequest {}
