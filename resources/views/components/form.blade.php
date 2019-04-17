<form method="{{ (isset($method) && $method === 'GET') ? 'GET' : 'POST' }}" action="{{ is_array($route) ? route(...$route) : route($route) }}"{{ ((!isset($method) || $method !== 'GET') && isset($enctype)) ? ('enctype=' . $enctype) : ''}}>
    @method($method ?? (is_array($route) ? route_method(...$route) : route_method($route)))
    @csrf
    {{ $slot }}
</form>
