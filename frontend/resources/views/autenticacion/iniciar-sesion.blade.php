{{-- Usa el layout centrado para visitantes. --}}
@extends('layouts.invitado')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Iniciar sesión')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el encabezado de la página. --}}
    <div class="space-y-1">
        {{-- Muestra el título principal. --}}
        <h1 class="text-2xl font-semibold text-slate-900">Iniciar sesión</h1>
        <h1 class="text-2xl font-semibold text-slate-900">UN GUSTO ESTAR AQUI EN LA INDOAMERICA</h1>
        
        {{-- Muestra una breve indicación para el usuario. --}}
        <p class="text-sm text-slate-500">Ingrese su correo electrónico y su contraseña.</p>
    </div>

    {{-- Define el formulario encargado de enviar las credenciales del usuario. --}}
    <form action="{{ route('sesion.iniciar') }}" method="POST" class="space-y-4">

        {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
        @csrf

        {{-- Muestra el campo del correo electrónico con su valor anterior y su error. --}}
        <x-campo-formulario nombre="correo_electronico" placeholder="correo@ejemplo.com" etiqueta="Correo electrónico" tipo="email" autocomplete="email" required autofocus />

        {{-- Muestra el campo de la contraseña y su error. --}}
        <x-campo-formulario nombre="contrasena" placeholder="••••••••" etiqueta="Contraseña" tipo="password" autocomplete="current-password" required />

        {{-- Muestra el botón que envía las credenciales. --}}
        <x-boton class="w-full">Iniciar sesión</x-boton>
    </form>

    {{-- Enlaza al formulario de registro para quienes no tienen cuenta. --}}
    <p class="text-center text-sm text-slate-600">
        ¿No tiene una cuenta?
        {{-- Muestra el enlace al registro. --}}
        <a href="{{ route('registro.formulario') }}" class="font-medium text-indigo-700 hover:underline">Regístrese</a>
    </p>
@endsection
