<form method="{{ (isset($method) && $method === 'GET') ? 'GET' : 'POST' }}" action="{{ route(...Arr::wrap($route)) }}" {{ (!(isset($method) && $method === 'GET') && isset($enctype)) ? ('enctype=' . $enctype) : ''}}>
    @method($method ?? (route_method(...Arr::wrap($route))))
    @csrf
    {{ $slot }}
</form>
