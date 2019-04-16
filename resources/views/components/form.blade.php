<form method="{{ (isset($method) && $method === 'GET') ? 'GET' : 'POST' }}" action="{{ is_array($route) ? route(...$route) : route($route) }}" enctype="{{isset($enctype) ? $enctype : ''}}">
    @method($method ?? (is_array($route) ? route_method(...$route) : route_method($route)))
    @csrf
    {{ $slot }}
</form>
