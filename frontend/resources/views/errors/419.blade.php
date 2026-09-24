{{-- Usa el layout principal para conservar la navegación del usuario autenticado. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Página expirada')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el aviso de formulario caducado por el token CSRF. --}}
    <x-pagina-error codigo="419" titulo="Página expirada" descripcion="El formulario caducó por inactividad. Vuelva a cargar la página e inténtelo de nuevo." />
@endsection
