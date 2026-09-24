<?php

// Declara el espacio de nombres de las solicitudes de la API.

namespace App\Http\Requests\Api;

// Importa el contrato de reglas de validación para el tipado del PHPDoc.
use Illuminate\Contracts\Validation\ValidationRule;
// Importa la clase base de las solicitudes con validación de Laravel.
use Illuminate\Foundation\Http\FormRequest;
// Importa la regla de contraseña con los requisitos predeterminados de la aplicación.
use Illuminate\Validation\Rules\Password;

/**
 * Valida los datos necesarios para registrar un usuario.
 */
class RegistroUsuarioRequest extends FormRequest
{
    /**
     * Determina si la solicitud está autorizada.
     */
    public function authorize(): bool
    {
        // Permite la solicitud, ya que el registro es público.
        return true;
    }

    /**
     * Define las reglas de validación de los datos del usuario.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Devuelve las reglas aplicadas a cada campo del formulario.
        return [
            // Exige un nombre de texto con un máximo de 255 caracteres.
            'nombre' => ['required', 'string', 'max:255'],
            // Exige un correo válido, en minúsculas y que no esté registrado en la columna "email".
            'correo_electronico' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            // Exige una contraseña confirmada con "contrasena_confirmation" que cumpla los requisitos de seguridad.
            'contrasena' => ['required', 'confirmed', Password::defaults()],
            // Exige una cédula ecuatoriana de diez dígitos que no esté registrada.
            'cedula' => ['required', 'digits:10', 'unique:users,cedula'],
            // Permite un teléfono opcional con un máximo de 20 caracteres.
            'telefono' => ['nullable', 'string', 'max:20'],
            // Permite una dirección opcional con un máximo de 255 caracteres.
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Define los mensajes de validación personalizados en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Devuelve los mensajes asociados a cada regla de validación.
        return [
            // Mensaje para los campos obligatorios.
            'required' => 'El campo :attribute es obligatorio.',
            // Mensaje para los campos que deben ser texto.
            'string' => 'El campo :attribute debe ser un texto.',
            // Mensaje para los campos que superan la longitud máxima.
            'max' => 'El campo :attribute no debe superar los :max caracteres.',
            // Mensaje para los correos con formato inválido.
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            // Mensaje para los correos que contienen mayúsculas.
            'lowercase' => 'El campo :attribute debe estar en minúsculas.',
            // Mensaje para los valores que ya están registrados.
            'unique' => 'El :attribute ya se encuentra registrado.',
            // Mensaje para la contraseña que no coincide con su confirmación.
            'confirmed' => 'La confirmación de la :attribute no coincide.',
            // Mensaje para la cédula que no tiene diez dígitos.
            'digits' => 'El campo :attribute debe tener :digits dígitos.',
            // Mensaje para la contraseña que no alcanza la longitud mínima.
            'contrasena.min' => 'La :attribute debe tener al menos :min caracteres.',
        ];
    }

    /**
     * Define los nombres legibles en español de los campos.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        // Devuelve el nombre mostrado para cada campo en los mensajes de error.
        return [
            // Nombre legible del campo de nombre.
            'nombre' => 'nombre',
            // Nombre legible del campo de correo.
            'correo_electronico' => 'correo electrónico',
            // Nombre legible del campo de contraseña.
            'contrasena' => 'contraseña',
            // Nombre legible del campo de cédula.
            'cedula' => 'cédula',
            // Nombre legible del campo de teléfono.
            'telefono' => 'teléfono',
            // Nombre legible del campo de dirección.
            'direccion' => 'dirección',
        ];
    }
}
