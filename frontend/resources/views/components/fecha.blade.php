{{-- Declara la propiedad con la fecha en formato ISO 8601 recibida de la API. --}}
@props(['valor' => null])

{{-- Muestra la fecha formateada cuando existe o un guion cuando está vacía. --}}
@if (filled($valor))
    {{-- Muestra la fecha como día/mes/año hora:minutos y conserva el valor original en el atributo datetime. --}}
    <time datetime="{{ $valor }}" {{ $attributes }}>{{ \Illuminate\Support\Carbon::parse($valor)->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</time>
@else
    {{-- Indica que no hay fecha disponible. --}}
    <span {{ $attributes->class('text-slate-400') }}>—</span>
@endif
