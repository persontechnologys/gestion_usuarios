{{-- Declara las propiedades: nombre del campo en la API, etiqueta, tipo, valor inicial y texto de ayuda. --}}
@props(['nombre', 'etiqueta', 'tipo' => 'text', 'valor' => null, 'ayuda' => null])

{{-- Agrupa la etiqueta, el campo, la ayuda y el error. --}}
<div class="space-y-1">

    {{-- Muestra la etiqueta asociada al campo. --}}
    <label for="{{ $nombre }}" class="block text-sm font-medium text-slate-700">{{ $etiqueta }}</label>

    {{-- Define el campo; las contraseñas nunca reciben un valor anterior y los demás conservan old() o el valor inicial. --}}
    <input
        type="{{ $tipo }}"
        name="{{ $nombre }}"
        id="{{ $nombre }}"
        @if ($tipo !== 'password') value="{{ old($nombre, $valor) }}" @endif
        @error($nombre) aria-invalid="true" aria-describedby="{{ $nombre }}-error" @enderror
        {{ $attributes->class([
            'block w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2',
            'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200' => ! $errors->has($nombre),
            'border-red-400 focus:border-red-500 focus:ring-red-200' => $errors->has($nombre),
        ]) }}
    >

    {{-- Muestra el texto de ayuda cuando se indica. --}}
    @if ($ayuda)
        <p class="text-xs text-slate-500">{{ $ayuda }}</p>
    @endif

    {{-- Muestra el error de validación del campo. --}}
    @error($nombre)
        <p id="{{ $nombre }}-error" class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
