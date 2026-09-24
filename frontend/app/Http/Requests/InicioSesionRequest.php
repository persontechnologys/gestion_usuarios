<?php

namespace App\Http\Requests;

// Importa el contrato de reglas de validación para el tipado del PHPDoc.
use Illuminate\Contracts\Validation\ValidationRule;
// Importa la clase base de las solicitudes con validación de Laravel.
use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida las credenciales del formulario de inicio de sesión antes de enviarlas a la API.
 */
class InicioSesionRequest extends FormRequest
{
    /**
     * Determina si la solicitud está autorizada.
     */
    public function authorize(): bool
    {
        // Permite la solicitud, ya que el inicio de sesión es público.
        return true;
    }

    /**
     * Define las mismas reglas que aplica la API a las credenciales.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Devuelve las reglas aplicadas a las credenciales.
        return [
            // Exige un correo electrónico con formato válido.
            'correo_electronico' => ['required', 'string', 'email'],
            // Exige la contraseña del usuario.
            'contrasena' => ['required', 'string'],
        ];
    }

    /**
     * Define los mensajes de validación en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Devuelve los mensajes asociados a cada regla de validación.
        return [
            // Mensaje para el correo obligatorio.
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            // Mensaje para el correo que no es texto.
            'correo_electronico.string' => 'El correo electrónico debe ser un texto.',
            // Mensaje para el correo con formato inválido.
            'correo_electronico.email' => 'El correo electrónico debe tener un formato válido.',
            // Mensaje para la contraseña obligatoria.
            'contrasena.required' => 'La contraseña es obligatoria.',
            // Mensaje para la contraseña que no es texto.
            'contrasena.string' => 'La contraseña debe ser un texto.',
        ];
    }
}
