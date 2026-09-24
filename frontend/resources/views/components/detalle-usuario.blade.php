{{-- Declara la propiedad con los datos del usuario recibidos de la API. --}}
@props(['usuario'])

{{-- Define la lista de datos del usuario en una o dos columnas según el ancho de pantalla. --}}
<dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">

    {{-- Muestra el nombre del usuario. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nombre</dt>
        <dd class="mt-1 text-slate-900">{{ $usuario['nombre'] }}</dd>
    </div>

    {{-- Muestra el correo electrónico del usuario. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Correo electrónico</dt>
        <dd class="mt-1 break-all text-slate-900">{{ $usuario['correo_electronico'] }}</dd>
    </div>

    {{-- Muestra la cédula del usuario. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Cédula</dt>
        <dd class="mt-1 text-slate-900">{{ $usuario['cedula'] }}</dd>
    </div>

    {{-- Muestra el teléfono o un guion si no lo tiene. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Teléfono</dt>
        <dd class="mt-1 text-slate-900">{{ $usuario['telefono'] ?: '—' }}</dd>
    </div>

    {{-- Muestra la dirección o un guion si no la tiene, ocupando todo el ancho. --}}
    <div class="sm:col-span-2">
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Dirección</dt>
        <dd class="mt-1 text-slate-900">{{ $usuario['direccion'] ?: '—' }}</dd>
    </div>

    {{-- Muestra si el correo está verificado y desde cuándo. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Correo verificado</dt>
        <dd class="mt-1 text-slate-900">
            {{-- Muestra la fecha de verificación o indica que no está verificado. --}}
            @if (filled($usuario['correo_verificado_en'] ?? null))
                <x-fecha :valor="$usuario['correo_verificado_en']" />
            @else
                No verificado
            @endif
        </dd>
    </div>

    {{-- Muestra la fecha de creación del usuario. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Fecha de creación</dt>
        <dd class="mt-1 text-slate-900"><x-fecha :valor="$usuario['fecha_creacion'] ?? null" /></dd>
    </div>

    {{-- Muestra la fecha de la última actualización del usuario. --}}
    <div>
        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Última actualización</dt>
        <dd class="mt-1 text-slate-900"><x-fecha :valor="$usuario['fecha_actualizacion'] ?? null" /></dd>
    </div>
</dl>
