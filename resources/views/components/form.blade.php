<form method="{{ $method ?? 'POST' }}" action="{{ route($route) }}">
    @csrf
    {{ $slot }}
</form>
