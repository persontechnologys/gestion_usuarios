<!DOCTYPE html>
{{-- Declara el idioma español del documento. --}}
<html lang="es">
<head>
    {{-- Define la codificación de caracteres. --}}
    <meta charset="utf-8">
    {{-- Ajusta el ancho de la página al dispositivo. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Muestra el título de la página seguido del nombre del sistema. --}}
    <title>@yield('titulo') · Gestión de Usuarios</title>
    {{-- Precarga el servidor de la fuente tipográfica. --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- Carga la fuente Instrument Sans definida en el tema de Tailwind. --}}
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    {{-- Carga los estilos y el JavaScript compilados por Vite. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">

    {{-- Centra el contenido vertical y horizontalmente en la pantalla. --}}
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10">

        {{-- Muestra el nombre del sistema encima de la tarjeta. --}}
        <p class="mb-6 text-xl font-semibold text-indigo-700">Gestión de Usuarios</p>

        {{-- Define la tarjeta que contiene el formulario. --}}
        <div class="w-full max-w-md space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            {{-- Muestra el mensaje de éxito guardado en la sesión. --}}
            <x-alerta-mensaje tipo="exito" :mensaje="session('exito')" />

            {{-- Muestra el mensaje de error guardado en la sesión. --}}
            <x-alerta-mensaje tipo="error" :mensaje="session('error')" />

            {{-- Inserta el contenido propio de cada página. --}}
            @yield('contenido')
        </div>
    </main>
</body>
</html>
