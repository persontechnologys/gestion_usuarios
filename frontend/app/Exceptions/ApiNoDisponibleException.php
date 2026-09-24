<?php

namespace App\Exceptions;

// Importa la excepción HTTP de Symfony para responder con el código 503.
use Symfony\Component\HttpKernel\Exception\HttpException;
// Importa la interfaz común de los errores de PHP para encadenar la causa original.
use Throwable;

/**
 * Indica que la API no respondió (fallo de conexión) o devolvió un error 5xx.
 *
 * Extiende HttpException con el código 503 para que, cuando falla la carga de
 * una página (GET), Laravel muestre la vista errors/503; en los envíos de
 * formularios, bootstrap/app.php vuelve atrás con el mensaje.
 */
class ApiNoDisponibleException extends HttpException
{
    /**
     * Mensaje en español que se muestra al usuario.
     */
    public const MENSAJE = 'No se pudo conectar con el servidor. Intente nuevamente.';

    /**
     * Crea la excepción con el código 503 y la causa original opcional.
     */
    public function __construct(?Throwable $causa = null)
    {
        // Asigna el código 503 (servicio no disponible), el mensaje en español y la excepción original.
        parent::__construct(503, self::MENSAJE, $causa);
    }
}
