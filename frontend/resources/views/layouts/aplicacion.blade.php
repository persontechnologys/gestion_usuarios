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

    {{-- Define la barra superior de navegación. --}}
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3" aria-label="Navegación principal">

            {{-- Muestra el nombre del sistema como enlace a la página de inicio. --}}
            <a href="{{ route('inicio') }}" class="text-lg font-semibold text-indigo-700">Gestión de Usuarios</a>

            {{-- Muestra los enlaces y el cierre de sesión solo si hay un usuario autenticado. --}}
            @if ($usuarioAutenticado)
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">

                    {{-- Enlaza al listado de usuarios y lo resalta si es la sección actual. --}}
                    <a href="{{ route('usuarios.indice') }}" @class(['font-medium hover:text-indigo-700', 'text-indigo-700' => request()->is('usuarios*'), 'text-slate-600' => ! request()->is('usuarios*')])>Usuarios</a>

                    {{-- Enlaza al perfil del usuario autenticado y lo resalta si es la sección actual. --}}
                    <a href="{{ route('perfil.mostrar') }}" @class(['font-medium hover:text-indigo-700', 'text-indigo-700' => request()->is('perfil'), 'text-slate-600' => ! request()->is('perfil')])>Mi perfil</a>

                    {{-- Saluda al usuario autenticado con su nombre. --}}
                    <span class="text-slate-500">Hola, {{ $usuarioAutenticado['nombre'] }}</span>

                    {{-- Define el formulario que cierra la sesión mediante POST. --}}
                    <form action="{{ route('sesion.cerrar') }}" method="POST">
                        {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
                        @csrf
                        {{-- Muestra el botón que envía el cierre de sesión. --}}
                        <x-boton variante="secundario" class="px-3 py-1.5">Cerrar sesión</x-boton>
                    </form>
                </div>
            @endif
        </nav>
    </header>

    {{-- Define el contenido principal de la página. --}}
    <main class="mx-auto max-w-6xl space-y-6 px-4 py-8">

        {{-- Muestra el mensaje de éxito guardado en la sesión. --}}
        <x-alerta-mensaje tipo="exito" :mensaje="session('exito')" />

        {{-- Muestra el mensaje de error guardado en la sesión. --}}
        <x-alerta-mensaje tipo="error" :mensaje="session('error')" />

        {{-- Inserta el contenido propio de cada página. --}}
        @yield('contenido')
    </main>
</body>
</html>
