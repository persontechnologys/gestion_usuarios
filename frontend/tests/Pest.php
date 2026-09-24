<?php

// Importa el servicio de autenticación para leer la clave del usuario en la sesión.
use App\Services\AutenticacionService;
// Importa el cliente de la API para leer la clave del token en la sesión.
use App\Services\ClienteApi;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Devuelve los datos de sesión de un usuario autenticado con la API.
 *
 * @return array<string, mixed>
 */
function sesionApi(): array
{
    // Devuelve el token y los datos básicos del usuario con id 1.
    return [
        ClienteApi::CLAVE_TOKEN => 'token-de-prueba',
        AutenticacionService::CLAVE_USUARIO => ['id' => 1, 'nombre' => 'Ana Pérez', 'correo_electronico' => 'ana@correo.com'],
    ];
}

/**
 * Devuelve un usuario con el formato de la API, con la posibilidad de sobrescribir campos.
 *
 * @param  array<string, mixed>  $cambios
 * @return array<string, mixed>
 */
function usuarioApi(array $cambios = []): array
{
    // Combina los datos de ejemplo con los cambios indicados.
    return array_merge([
        'id' => 2,
        'nombre' => 'Luis Gómez',
        'correo_electronico' => 'luis@correo.com',
        'cedula' => '0102030405',
        'telefono' => '0991234567',
        'direccion' => 'Av. Principal 123',
        'correo_verificado_en' => null,
        'fecha_creacion' => '2026-09-01T10:30:00+00:00',
        'fecha_actualizacion' => '2026-09-02T08:00:00+00:00',
    ], $cambios);
}
