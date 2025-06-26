<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="csp-nonce" nonce="{{ Vite::cspNonce() }}">
    @yield('head')
    @if(app()->view->getSections()['pagetitle'] ?? false)
        <title>{{__('t.global.page_title')}} - @yield('pagetitle')</title>
    @else
        <title>{{__('t.global.page_title')}}</title>
    @endif
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
</head>
<body>

@yield('layout')

<div id="laravel-data" data-laravel="{{ json_encode([
    'oldInput' => (object) Session::getOldInput(),
    'errors' => (object) $errors->get('*'),
    'routes' => collect(Route::getRoutes())->mapWithKeys(function (\Illuminate\Routing\Route $route) { return [$route->getName() => [ 'uri' => '/'.$route->uri(), 'method' => head($route->methods())]]; }),
    'csrf' => csrf_token(),
    'signalingServers' => explode(' ', config('app.collaboration.signaling_servers')),
    'username' => Auth::user() ? Auth::user()->name : null,
]) }}"></div>
@vite('resources/js/app.js')
</body>
</html>
