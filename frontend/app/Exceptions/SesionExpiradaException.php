<?php

namespace App\Exceptions;

// Importa la clase base de las excepciones de PHP.
use Exception;

/**
 * Indica que la API rechazó el token de acceso (respuesta 401).
 *
 * Se renderiza en bootstrap/app.php: elimina el token de la sesión y
 * redirige al formulario de inicio de sesión con el mensaje correspondiente.
 */
class SesionExpiradaException extends Exception
{
    /**
     * Crea la excepción con el mensaje de sesión expirada.
     */
    public function __construct()
    {
        // Asigna el mensaje en español que describe la causa de la excepción.
        parent::__construct('Su sesión ha expirado.');
    }
}
