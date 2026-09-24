{{-- Usa el layout sencillo para visitantes, que depende de menos datos si la aplicación falló. --}}
@extends('layouts.invitado')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Error del servidor')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el aviso de error interno. --}}
    <x-pagina-error codigo="500" titulo="Error del servidor" descripcion="Ocurrió un error inesperado. Intente nuevamente en unos minutos." />
@endsection
