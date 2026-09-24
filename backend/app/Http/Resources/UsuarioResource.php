<?php

// Declara el espacio de nombres de los recursos de la API.

namespace App\Http\Resources;

// Importa el modelo de usuarios para el tipado del PHPDoc.
use App\Models\User;
// Importa la clase de solicitud HTTP.
use Illuminate\Http\Request;
// Importa la clase base de los recursos JSON de Eloquent.
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforma un usuario en su representación JSON pública, sin datos sensibles.
 *
 * @mixin User
 */
class UsuarioResource extends JsonResource
{
    /**
     * Convierte el usuario en un arreglo para la respuesta JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Devuelve únicamente los campos que pueden exponerse en la API.
        return [
            // Identificador del usuario.
            'id' => $this->id,
            // Nombre del usuario.
            'nombre' => $this->name,
            // Correo electrónico del usuario.
            'correo_electronico' => $this->email,
            // Cédula de identidad del usuario.
            'cedula' => $this->cedula,
            // Teléfono del usuario.
            'telefono' => $this->telefono,
            // Dirección del usuario.
            'direccion' => $this->direccion,
            // Fecha de verificación del correo en formato ISO 8601.
            'correo_verificado_en' => $this->email_verified_at?->toIso8601String(),
            // Fecha de creación del registro en formato ISO 8601.
            'fecha_creacion' => $this->created_at?->toIso8601String(),
            // Fecha de la última actualización en formato ISO 8601.
            'fecha_actualizacion' => $this->updated_at?->toIso8601String(),
        ];
    }
}
