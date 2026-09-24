{{-- Usa el layout principal de la aplicación. --}}
@extends('layouts.aplicacion')

{{-- Define el título de la pestaña del navegador. --}}
@section('titulo', 'Usuarios')

{{-- Define el contenido de la página. --}}
@section('contenido')
    {{-- Muestra el encabezado con el título y el botón para crear usuarios. --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        {{-- Muestra el título y el total de usuarios. --}}
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Usuarios</h1>
            {{-- Muestra el total de usuarios registrados. --}}
            <p class="text-sm text-slate-500">{{ $paginacion['total'] ?? 0 }} usuarios registrados</p>
        </div>

        {{-- Enlaza al formulario de creación de usuarios. --}}
        <a href="{{ route('usuarios.crear') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">Nuevo usuario</a>
    </div>

    {{-- Muestra el estado vacío cuando no hay usuarios. --}}
    @if (empty($usuarios))
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
            {{-- Muestra el mensaje de estado vacío. --}}
            <p class="text-slate-600">No hay usuarios registrados</p>
        </div>
    @else
        {{-- Define el contenedor de la tabla con desplazamiento horizontal en pantallas estrechas. --}}
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">

                {{-- Define los encabezados de las columnas. --}}
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3">Nombre</th>
                        <th scope="col" class="px-4 py-3">Correo electrónico</th>
                        <th scope="col" class="px-4 py-3">Cédula</th>
                        <th scope="col" class="px-4 py-3">Teléfono</th>
                        <th scope="col" class="px-4 py-3">Fecha de creación</th>
                        <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>

                {{-- Define el cuerpo de la tabla con una fila por usuario. --}}
                <tbody class="divide-y divide-slate-100">
                    {{-- Recorre los usuarios de la página actual. --}}
                    @foreach ($usuarios as $usuario)
                        <tr class="hover:bg-slate-50">
                            {{-- Muestra el nombre del usuario. --}}
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">{{ $usuario['nombre'] }}</td>
                            {{-- Muestra el correo electrónico del usuario. --}}
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $usuario['correo_electronico'] }}</td>
                            {{-- Muestra la cédula del usuario. --}}
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $usuario['cedula'] }}</td>
                            {{-- Muestra el teléfono o un guion si no lo tiene. --}}
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $usuario['telefono'] ?: '—' }}</td>
                            {{-- Muestra la fecha de creación formateada. --}}
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600"><x-fecha :valor="$usuario['fecha_creacion'] ?? null" /></td>
                            {{-- Muestra las acciones disponibles para el usuario. --}}
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- Enlaza al detalle del usuario. --}}
                                    <a href="{{ route('usuarios.mostrar', $usuario['id']) }}" class="font-medium text-slate-600 hover:text-indigo-700">Ver</a>
                                    {{-- Enlaza al formulario de edición del usuario. --}}
                                    <a href="{{ route('usuarios.editar', $usuario['id']) }}" class="font-medium text-indigo-700 hover:text-indigo-900">Editar</a>
                                    {{-- Muestra el formulario de eliminación con confirmación. --}}
                                    <x-formulario-eliminar :usuario="$usuario">
                                        {{-- Muestra el botón que envía la eliminación. --}}
                                        <button type="submit" class="font-medium text-red-600 hover:text-red-800">Eliminar</button>
                                    </x-formulario-eliminar>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Define la navegación entre páginas. --}}
        <nav class="flex flex-wrap items-center justify-between gap-3 text-sm" aria-label="Paginación">

            {{-- Muestra el enlace a la página anterior o un texto deshabilitado en la primera página. --}}
            @if ($paginacion['current_page'] > 1)
                <a href="{{ route('usuarios.indice', ['page' => $paginacion['current_page'] - 1]) }}" rel="prev" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">Anterior</a>
            @else
                <span class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-1.5 text-slate-400" aria-disabled="true">Anterior</span>
            @endif

            {{-- Muestra la página actual y el total de páginas. --}}
            <span class="text-slate-600">Página {{ $paginacion['current_page'] }} de {{ $paginacion['last_page'] }}</span>

            {{-- Muestra el enlace a la página siguiente o un texto deshabilitado en la última página. --}}
            @if ($paginacion['current_page'] < $paginacion['last_page'])
                <a href="{{ route('usuarios.indice', ['page' => $paginacion['current_page'] + 1]) }}" rel="next" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">Siguiente</a>
            @else
                <span class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-1.5 text-slate-400" aria-disabled="true">Siguiente</span>
            @endif
        </nav>
    @endif
@endsection
