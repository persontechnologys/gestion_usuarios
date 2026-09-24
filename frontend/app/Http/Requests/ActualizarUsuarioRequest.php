<?php

namespace App\Http\Requests;

// Importa el contrato de reglas de validación para el tipado del PHPDoc.
use Illuminate\Contracts\Validation\ValidationRule;
// Importa la regla de contraseñas con los requisitos por defecto (mínimo 8 caracteres).
use Illuminate\Validation\Rules\Password;

/**
 * Valida los datos del formulario de edición de usuarios antes de enviarlos a la API.
 *
 * Hereda los mensajes y los nombres de campo de RegistroRequest; la contraseña es opcional.
 */
class ActualizarUsuarioRequest extends RegistroRequest
{
    /**
     * Define las mismas reglas que aplica la API a la actualización.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Devuelve las reglas; "sometimes" valida cada campo solo cuando se envía, igual que la API.
        return [
            // Valida el nombre cuando se envía.
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            // Valida el correo cuando se envía.
            'correo_electronico' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255'],
            // Valida la nueva contraseña solo si se escribe.
            'contrasena' => ['nullable', 'confirmed', Password::defaults()],
            // Acepta la confirmación para incluirla en los datos enviados a la API.
            'contrasena_confirmation' => ['nullable', 'string'],
            // Valida la cédula cuando se envía.
            'cedula' => ['sometimes', 'required', 'digits:10'],
            // Valida el teléfono opcional.
            'telefono' => ['sometimes', 'nullable', 'string', 'max:20'],
            // Valida la dirección opcional.
            'direccion' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Devuelve los datos validados sin la contraseña cuando se dejó en blanco.
     *
     * @return array<string, mixed>
     */
    public function datosParaApi(): array
    {
        // Obtiene los datos que superaron la validación.
        $datos = $this->validated();

        // Comprueba si el usuario no escribió una contraseña nueva.
        if (blank($datos['contrasena'] ?? null)) {
            // Quita la contraseña y su confirmación para que la API conserve la actual.
            unset($datos['contrasena'], $datos['contrasena_confirmation']);
        }

        // Devuelve los datos listos para enviarse a la API.
        return $datos;
    }
}
