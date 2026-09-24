{{-- Declara las propiedades: el tipo de alerta (exito o error) y el mensaje a mostrar. --}}
@props(['tipo' => 'exito', 'mensaje' => null])

{{-- Muestra la alerta solo cuando hay un mensaje. --}}
@if (filled($mensaje))
    {{-- Define el contenedor de la alerta con colores según su tipo; los errores se anuncian de inmediato a los lectores de pantalla. --}}
    <div
        role="{{ $tipo === 'error' ? 'alert' : 'status' }}"
        {{ $attributes->class([
            'rounded-lg border px-4 py-3 text-sm',
            'border-emerald-200 bg-emerald-50 text-emerald-800' => $tipo === 'exito',
            'border-red-200 bg-red-50 text-red-800' => $tipo === 'error',
        ]) }}
    >
        {{-- Muestra el texto del mensaje escapado. --}}
        {{ $mensaje }}
    </div>
@endif
