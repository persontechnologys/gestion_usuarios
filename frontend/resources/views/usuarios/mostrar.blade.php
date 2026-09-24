{{-- Usa el layout principal de la aplicación. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', $usuario['nombre'])

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Define la tarjeta con el detalle del usuario. --}}
    <div class="mx-auto max-w-2xl space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        {{-- Muestra el título de la página. --}}
        <h1 class="text-2xl font-semibold text-slate-900">Detalle del usuario</h1>

        {{-- Muestra los datos del usuario. --}}
        <x-detalle-usuario :usuario="$usuario" />

        {{-- Agrupa las acciones disponibles. --}}
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
            {{-- Enlaza de vuelta al listado de usuarios. --}}
            <a href="{{ route('usuarios.indice') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Volver al listado</a>

            {{-- Muestra el formulario de eliminación con confirmación. --}}
            <x-formulario-eliminar :usuario="$usuario" class="flex">
                {{-- Muestra el botón que envía la eliminación. --}}
                <x-boton variante="peligro" class="w-full">Eliminar</x-boton>
            </x-formulario-eliminar>

            {{-- Enlaza al formulario de edición. --}}
            <a href="{{ route('usuarios.editar', $usuario['id']) }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Editar</a>
        </div>
    </div>
@endsection
