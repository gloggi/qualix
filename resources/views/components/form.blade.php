<form method="{{ $method ?? 'POST' }}" action="{{ route($route) }}">
    @method($method ?? route_method($route))
    @csrf
    {{ $slot }}
</form>
