<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Importa el trait de Sanctum que permite emitir tokens de acceso personal para la API.
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Incorpora los traits de factory, tokens de API de Sanctum y notificaciones.
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Permite la asignación masiva de la cédula de identidad ecuatoriana.
        'cedula',
        // Permite la asignación masiva del número de teléfono.
        'telefono',
        // Permite la asignación masiva de la dirección domiciliaria.
        'direccion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
