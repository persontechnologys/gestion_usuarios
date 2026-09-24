{{-- Usa el layout principal de la aplicación. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Editar usuario')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Define la tarjeta que contiene el formulario. --}}
    <div class="mx-auto max-w-2xl space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        {{-- Muestra el título con el nombre del usuario que se edita. --}}
        <h1 class="text-2xl font-semibold text-slate-900">Editar a {{ $usuario['nombre'] }}</h1>

        {{-- Incluye el formulario compartido configurado para editar. --}}
        @include('usuarios.formulario', [
            'accion' => route('usuarios.actualizar', $usuario['id']),
            'metodo' => 'PUT',
            'textoBoton' => 'Guardar cambios',
        ])
    </div>
@endsection
