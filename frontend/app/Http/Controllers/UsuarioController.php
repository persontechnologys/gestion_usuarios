<?php

namespace App\Http\Controllers;

// Importa la solicitud que valida la edición de un usuario.
use App\Http\Requests\ActualizarUsuarioRequest;
// Importa la solicitud que valida la creación de un usuario.
use App\Http\Requests\GuardarUsuarioRequest;
// Importa el servicio que gestiona la sesión con la API.
use App\Services\AutenticacionService;
// Importa el servicio que realiza las operaciones de usuarios en la API.
use App\Services\UsuarioService;
// Importa la respuesta de redirección.
use Illuminate\Http\RedirectResponse;
// Importa la solicitud HTTP para leer el número de página.
use Illuminate\Http\Request;
// Importa el tipo de las vistas Blade.
use Illuminate\View\View;

/**
 * Gestiona el listado, la creación, el detalle, la edición y la eliminación de usuarios.
 */
class UsuarioController extends Controller
{
    /**
     * Recibe los servicios de usuarios y de autenticación mediante inyección de dependencias.
     */
    public function __construct(
        private UsuarioService $usuarioService,
        private AutenticacionService $autenticacionService,
    ) {}

    /**
     * Muestra una página del listado de usuarios.
     */
    public function index(Request $solicitud): View|RedirectResponse
    {
        // Lee el número de página de la URL y lo limita a un mínimo de 1.
        $pagina = max(1, $solicitud->integer('page', 1));

        // Solicita a la API la página indicada.
        $resultado = $this->usuarioService->listar($pagina);

        // Comprueba si la página pedida está más allá de la última disponible.
        if ($pagina > 1 && $pagina > $resultado['meta']['last_page']) {
            // Redirige a la última página existente.
            return redirect()->route('usuarios.indice', ['page' => $resultado['meta']['last_page']]);
        }

        // Devuelve la vista con los usuarios y los datos de paginación.
        return view('usuarios.indice', [
            // Lista de usuarios de la página actual.
            'usuarios' => $resultado['data'],
            // Página actual, última página y total de registros.
            'paginacion' => $resultado['meta'],
        ]);
    }

    /**
     * Muestra el formulario de creación de usuarios.
     */
    public function create(): View
    {
        // Devuelve la vista con el formulario vacío.
        return view('usuarios.crear');
    }

    /**
     * Crea un usuario en la API con los datos validados.
     */
    public function store(GuardarUsuarioRequest $solicitud): RedirectResponse
    {
        // Envía a la API los datos validados del usuario nuevo.
        $usuario = $this->usuarioService->crear($solicitud->validated());

        // Redirige al listado con un mensaje de éxito.
        return redirect()->route('usuarios.indice')->with('exito', "El usuario {$usuario['nombre']} se creó correctamente.");
    }

    /**
     * Muestra el detalle de un usuario.
     */
    public function show(int $usuario): View
    {
        // Devuelve la vista con los datos del usuario obtenidos de la API.
        return view('usuarios.mostrar', ['usuario' => $this->usuarioService->obtener($usuario)]);
    }

    /**
     * Muestra el formulario de edición con los datos actuales del usuario.
     */
    public function edit(int $usuario): View
    {
        // Devuelve la vista con el formulario rellenado con los datos de la API.
        return view('usuarios.editar', ['usuario' => $this->usuarioService->obtener($usuario)]);
    }

    /**
     * Actualiza un usuario en la API con los datos validados.
     */
    public function update(ActualizarUsuarioRequest $solicitud, int $usuario): RedirectResponse
    {
        // Envía a la API los datos modificados, sin la contraseña si se dejó en blanco.
        $usuarioActualizado = $this->usuarioService->actualizar($usuario, $solicitud->datosParaApi());

        // Comprueba si el usuario editado es el mismo que tiene la sesión iniciada.
        if ($this->autenticacionService->esUsuarioAutenticado($usuario)) {
            // Actualiza el nombre y el correo guardados en la sesión para el layout.
            $this->autenticacionService->recordarUsuario($usuarioActualizado);
        }

        // Redirige al detalle del usuario con un mensaje de éxito.
        return redirect()->route('usuarios.mostrar', $usuario)->with('exito', 'Los datos del usuario se actualizaron correctamente.');
    }

    /**
     * Elimina un usuario en la API.
     */
    public function destroy(int $usuario): RedirectResponse
    {
        // Solicita a la API la eliminación del usuario.
        $this->usuarioService->eliminar($usuario);

        // Comprueba si el usuario eliminado es el mismo que tiene la sesión iniciada.
        if ($this->autenticacionService->esUsuarioAutenticado($usuario)) {
            // Cierra la sesión local, porque la API ya eliminó el token junto con la cuenta.
            $this->autenticacionService->cerrarSesionLocal();

            // Redirige al inicio de sesión informando que la cuenta se eliminó.
            return redirect()->route('sesion.formulario')->with('exito', 'Su cuenta se eliminó correctamente y la sesión se cerró.');
        }

        // Redirige al listado con un mensaje de éxito.
        return redirect()->route('usuarios.indice')->with('exito', 'El usuario se eliminó correctamente.');
    }
}
