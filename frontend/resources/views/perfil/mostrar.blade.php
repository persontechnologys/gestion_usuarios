{{-- Usa el layout principal de la aplicación. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Mi perfil')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Define la tarjeta con los datos del usuario autenticado. --}}
    <div class="mx-auto max-w-2xl space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        {{-- Muestra el título de la página. --}}
        <h1 class="text-2xl font-semibold text-slate-900">Mi perfil</h1>

        {{-- Muestra los datos del usuario autenticado obtenidos de /mi-perfil. --}}
        <x-detalle-usuario :usuario="$usuario" />

        {{-- Agrupa las acciones disponibles. --}}
        <div class="flex justify-end border-t border-slate-100 pt-6">
            {{-- Enlaza al formulario de edición de los propios datos. --}}
            <a href="{{ route('usuarios.editar', $usuario['id']) }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Editar mis datos</a>
        </div>
    </div>
@endsection
