<?php

// Importa el controlador del perfil del usuario autenticado.
use App\Http\Controllers\PerfilController;
// Importa el controlador del registro de cuentas.
use App\Http\Controllers\RegistroController;
// Importa el controlador del inicio y cierre de sesión.
use App\Http\Controllers\SesionController;
// Importa el controlador de la gestión de usuarios.
use App\Http\Controllers\UsuarioController;
// Importa el servicio que indica si hay una sesión con la API.
use App\Services\AutenticacionService;
// Importa la respuesta de redirección.
use Illuminate\Http\RedirectResponse;
// Importa la fachada para definir las rutas web.
use Illuminate\Support\Facades\Route;

// Redirige la raíz al listado de usuarios o al inicio de sesión según haya sesión o no.
Route::get('/', function (AutenticacionService $autenticacionService): RedirectResponse {
    // Comprueba si hay un token de la API en la sesión.
    if ($autenticacionService->haySesion()) {
        // Envía al usuario autenticado al listado de usuarios.
        return redirect()->route('usuarios.indice');
    }

    // Envía al visitante al formulario de inicio de sesión.
    return redirect()->route('sesion.formulario');
})->name('inicio');

// Agrupa las rutas accesibles solo para visitantes sin sesión.
Route::middleware('invitado.api')->group(function (): void {
    // Muestra el formulario de inicio de sesión.
    Route::get('/iniciar-sesion', [SesionController::class, 'mostrarFormulario'])->name('sesion.formulario');

    // Recibe las credenciales e inicia la sesión en la API.
    Route::post('/iniciar-sesion', [SesionController::class, 'iniciarSesion'])->name('sesion.iniciar');

    // Muestra el formulario de registro.
    Route::get('/registro', [RegistroController::class, 'mostrarFormulario'])->name('registro.formulario');

    // Recibe los datos del registro y crea la cuenta en la API.
    Route::post('/registro', [RegistroController::class, 'guardar'])->name('registro.guardar');
});

// Cierra la sesión; queda fuera de sesion.api para poder limpiar siempre la sesión local.
Route::post('/cerrar-sesion', [SesionController::class, 'cerrarSesion'])->name('sesion.cerrar');

// Agrupa las rutas que exigen una sesión con la API.
Route::middleware('sesion.api')->group(function (): void {
    // Define las rutas CRUD de usuarios con nombres en español.
    Route::resource('usuarios', UsuarioController::class)
        // Asigna un nombre en español a cada acción del recurso.
        ->names([
            'index' => 'usuarios.indice',
            'create' => 'usuarios.crear',
            'store' => 'usuarios.guardar',
            'show' => 'usuarios.mostrar',
            'edit' => 'usuarios.editar',
            'update' => 'usuarios.actualizar',
            'destroy' => 'usuarios.eliminar',
        ])
        // Acepta solo identificadores numéricos en el parámetro {usuario}.
        ->where(['usuario' => '[0-9]+']);

    // Muestra el perfil del usuario autenticado.
    Route::get('/perfil', [PerfilController::class, 'mostrar'])->name('perfil.mostrar');
});
