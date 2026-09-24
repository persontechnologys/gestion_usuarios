{{-- Declara la propiedad con los datos del usuario que se eliminará. --}}
@props(['usuario'])

{{-- Define el formulario de eliminación; data-confirmar contiene el mensaje que JavaScript pide confirmar antes de enviarlo. --}}
<form
    action="{{ route('usuarios.eliminar', $usuario['id']) }}"
    method="POST"
    data-confirmar="¿Desea eliminar a {{ $usuario['nombre'] }}? Esta acción no se puede deshacer."
    {{ $attributes->class('inline') }}
>
    {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
    @csrf

    {{-- Simula el método DELETE que espera la ruta de eliminación. --}}
    @method('DELETE')

    {{-- Muestra el contenido del botón de eliminación definido por la vista. --}}
    {{ $slot }}
</form>
