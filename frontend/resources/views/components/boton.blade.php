{{-- Declara las propiedades: el tipo del botón y su variante visual (primario, secundario o peligro). --}}
@props(['tipo' => 'submit', 'variante' => 'primario'])

{{-- Define el botón con estilos según la variante y permite añadir atributos adicionales. --}}
<button
    type="{{ $tipo }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:ring-indigo-500' => $variante === 'primario',
        'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400' => $variante === 'secundario',
        'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500' => $variante === 'peligro',
    ]) }}
>
    {{-- Muestra el contenido del botón. --}}
    {{ $slot }}
</button>
