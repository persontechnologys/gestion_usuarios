{{-- Declara las propiedades: el código HTTP, el título y la descripción del error. --}}
@props(['codigo', 'titulo', 'descripcion'])

{{-- Define el bloque centrado con la información del error. --}}
<div class="mx-auto max-w-lg space-y-4 py-10 text-center">

    {{-- Muestra el código HTTP del error. --}}
    <p class="text-5xl font-bold text-indigo-600">{{ $codigo }}</p>

    {{-- Muestra el título del error. --}}
    <h1 class="text-2xl font-semibold text-slate-900">{{ $titulo }}</h1>

    {{-- Muestra la explicación del error. --}}
    <p class="text-slate-600">{{ $descripcion }}</p>

    {{-- Enlaza a la página de inicio, que redirige según haya sesión o no. --}}
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Ir al inicio</a>
</div>
