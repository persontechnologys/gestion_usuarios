{{-- Vista parcial compartida por la creación y la edición; recibe $usuario (null al crear), $accion, $metodo y $textoBoton. --}}

{{-- Define el formulario que envía los datos del usuario. --}}
<form action="{{ $accion }}" method="POST" class="space-y-4">

    {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
    @csrf

    {{-- Simula el método PUT cuando el formulario edita un usuario. --}}
    @if ($metodo === 'PUT')
        @method('PUT')
    @endif

    {{-- Muestra el campo del nombre con el valor actual al editar. --}}
    <x-campo-formulario nombre="nombre" etiqueta="Nombre completo" :valor="$usuario['nombre'] ?? null" autocomplete="name" maxlength="255" required />

    {{-- Muestra el campo del correo electrónico con el valor actual al editar. --}}
    <x-campo-formulario nombre="correo_electronico" etiqueta="Correo electrónico" tipo="email" :valor="$usuario['correo_electronico'] ?? null" autocomplete="email" maxlength="255" required />

    {{-- Muestra el campo de la cédula con teclado numérico en el móvil. --}}
    <x-campo-formulario nombre="cedula" etiqueta="Cédula" :valor="$usuario['cedula'] ?? null" inputmode="numeric" maxlength="10" ayuda="Diez dígitos, sin guiones." required />

    {{-- Muestra el campo opcional del teléfono. --}}
    <x-campo-formulario nombre="telefono" etiqueta="Teléfono (opcional)" tipo="tel" :valor="$usuario['telefono'] ?? null" autocomplete="tel" maxlength="20" />

    {{-- Muestra el campo opcional de la dirección. --}}
    <x-campo-formulario nombre="direccion" etiqueta="Dirección (opcional)" :valor="$usuario['direccion'] ?? null" autocomplete="street-address" maxlength="255" />

    {{-- Muestra el campo de la contraseña, obligatorio solo al crear. --}}
    <x-campo-formulario
        nombre="contrasena"
        :etiqueta="$usuario ? 'Nueva contraseña (opcional)' : 'Contraseña'"
        tipo="password"
        autocomplete="new-password"
        :ayuda="$usuario ? 'Déjela en blanco para conservar la contraseña actual. Mínimo 8 caracteres.' : 'Mínimo 8 caracteres.'"
        :required="$usuario === null"
    />

    {{-- Muestra el campo de confirmación de la contraseña, obligatorio solo al crear. --}}
    <x-campo-formulario nombre="contrasena_confirmation" etiqueta="Confirmar contraseña" tipo="password" autocomplete="new-password" :required="$usuario === null" />

    {{-- Agrupa los botones de acción del formulario. --}}
    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
        {{-- Enlaza a la página anterior sin guardar cambios. --}}
        <a href="{{ $usuario ? route('usuarios.mostrar', $usuario['id']) : route('usuarios.indice') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancelar</a>

        {{-- Muestra el botón que envía el formulario. --}}
        <x-boton>{{ $textoBoton }}</x-boton>
    </div>
</form>
