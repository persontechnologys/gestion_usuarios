{{-- Usa el layout principal para conservar la navegación del usuario autenticado. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Servicio no disponible')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el aviso de que la API no responde. --}}
    <x-pagina-error codigo="503" titulo="Servicio no disponible" descripcion="No se pudo conectar con el servidor. Intente nuevamente." />
@endsection
