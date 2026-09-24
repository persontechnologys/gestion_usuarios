{{-- Usa el layout centrado para visitantes. --}}
@extends('layouts.invitado')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Registro')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el encabezado de la página. --}}
    <div class="space-y-1">
        {{-- Muestra el título principal. --}}
        <h1 class="text-2xl font-semibold text-slate-900">Crear cuenta</h1>
        {{-- Muestra una breve indicación para el usuario. --}}
        <p class="text-sm text-slate-500">Complete sus datos para registrarse.</p>
    </div>

    {{-- Define el formulario encargado de enviar los datos del registro. --}}
    <form action="{{ route('registro.guardar') }}" method="POST" class="space-y-4">

        {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
        @csrf

        {{-- Muestra el campo del nombre completo. --}}
        <x-campo-formulario nombre="nombre" etiqueta="Nombre completo" autocomplete="name" maxlength="255" required autofocus />

        {{-- Muestra el campo del correo electrónico. --}}
        <x-campo-formulario nombre="correo_electronico" etiqueta="Correo electrónico" tipo="email" autocomplete="email" maxlength="255" required />

        {{-- Muestra el campo de la cédula con teclado numérico en el móvil. --}}
        <x-campo-formulario nombre="cedula" etiqueta="Cédula" inputmode="numeric" maxlength="10" ayuda="Diez dígitos, sin guiones." required />

        {{-- Muestra el campo opcional del teléfono. --}}
        <x-campo-formulario nombre="telefono" etiqueta="Teléfono (opcional)" tipo="tel" autocomplete="tel" maxlength="20" />

        {{-- Muestra el campo opcional de la dirección. --}}
        <x-campo-formulario nombre="direccion" etiqueta="Dirección (opcional)" autocomplete="street-address" maxlength="255" />

        {{-- Muestra el campo de la contraseña. --}}
        <x-campo-formulario nombre="contrasena" etiqueta="Contraseña" tipo="password" autocomplete="new-password" ayuda="Mínimo 8 caracteres." required />

        {{-- Muestra el campo de confirmación de la contraseña. --}}
        <x-campo-formulario nombre="contrasena_confirmation" etiqueta="Confirmar contraseña" tipo="password" autocomplete="new-password" required />

        {{-- Muestra el botón que envía el registro. --}}
        <x-boton class="w-full">Registrarse</x-boton>
    </form>

    {{-- Enlaza al inicio de sesión para quienes ya tienen cuenta. --}}
    <p class="text-center text-sm text-slate-600">
        ¿Ya tiene una cuenta?
        {{-- Muestra el enlace al inicio de sesión. --}}
        <a href="{{ route('sesion.formulario') }}" class="font-medium text-indigo-700 hover:underline">Inicie sesión</a>
    </p>
@endsection
