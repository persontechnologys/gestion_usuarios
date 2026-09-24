<?php

// Importa el controlador de autenticación de la API.
use App\Http\Controllers\Api\AutenticacionController;
// Importa el controlador del CRUD de usuarios.
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Define la ruta pública para registrar un usuario nuevo.
Route::post('/registro', [AutenticacionController::class, 'registrar'])->name('api.registro');

// Define la ruta pública para iniciar sesión, limitada a 5 intentos por minuto por correo e IP.
Route::post('/iniciar-sesion', [AutenticacionController::class, 'iniciarSesion'])->middleware('throttle:inicio-sesion')->name('api.iniciar-sesion');

// Agrupa las rutas que requieren un token válido de Sanctum.
Route::middleware('auth:sanctum')->group(function () {
    // Define la ruta para cerrar la sesión y revocar el token actual.
    Route::post('/cerrar-sesion', [AutenticacionController::class, 'cerrarSesion'])->name('api.cerrar-sesion');

    // Define la ruta para consultar el perfil del usuario autenticado.
    Route::get('/mi-perfil', [AutenticacionController::class, 'miPerfil'])->name('api.mi-perfil');

    // Define las rutas del CRUD de usuarios (listar, crear, mostrar, actualizar y eliminar).
    // GET /api/usuarios - Listar usuarios
    // POST /api/usuarios - Crear usuario
    // GET /api/usuarios/{usuario} - Mostrar usuario
    // PUT /api/usuarios/{usuario} - Actualizar usuario
    // DELETE /api/usuarios/{usuario} - Eliminar usuario
    Route::apiResource('usuarios', UsuarioController::class)->names('api.usuarios');

});
