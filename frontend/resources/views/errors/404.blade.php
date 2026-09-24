{{-- Usa el layout principal para conservar la navegación del usuario autenticado. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Página no encontrada')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el aviso de recurso no encontrado. --}}
    <x-pagina-error codigo="404" titulo="Página no encontrada" descripcion="La página o el usuario que busca no existe o ya fue eliminado." />
@endsection
