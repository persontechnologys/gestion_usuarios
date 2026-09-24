<?php

// Declara el espacio de nombres de las solicitudes de la API.

namespace App\Http\Requests\Api;

// Importa el contrato de reglas de validación para el tipado del PHPDoc.
use Illuminate\Contracts\Validation\ValidationRule;
// Importa el constructor de reglas para ignorar el propio registro en las reglas de unicidad.
use Illuminate\Validation\Rule;
// Importa la regla de contraseña con los requisitos predeterminados de la aplicación.
use Illuminate\Validation\Rules\Password;

/**
 * Valida los datos para actualizar parcial o totalmente un usuario.
 *
 * Hereda los mensajes y atributos en español del registro.
 */
class ActualizarUsuarioRequest extends RegistroUsuarioRequest
{
    /**
     * Define las reglas de validación de la actualización.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtiene el identificador del usuario recibido en la ruta.
        $idUsuario = $this->route('usuario')?->id;

        // Devuelve las reglas; "sometimes" valida cada campo solo cuando se envía.
        return [
            // Valida el nombre cuando se envía.
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            // Valida el correo contra la columna "email" y permite conservar el correo actual del propio usuario.
            'correo_electronico' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($idUsuario)],
            // Valida la nueva contraseña, confirmada con "contrasena_confirmation", cuando se envía.
            'contrasena' => ['sometimes', 'nullable', 'confirmed', Password::defaults()],
            // Valida la cédula y permite conservar la cédula actual del propio usuario.
            'cedula' => ['sometimes', 'required', 'digits:10', Rule::unique('users', 'cedula')->ignore($idUsuario)],
            // Valida el teléfono opcional.
            'telefono' => ['sometimes', 'nullable', 'string', 'max:20'],
            // Valida la dirección opcional.
            'direccion' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
