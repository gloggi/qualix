<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{__('t.global.page_title')}}</title>
    <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <script type="application/javascript">
        {{-- Using @json is okay here because it's in the context of a <script> tag, not in a HTML attribute --}}
        window.oldInput = @json((object) Session::getOldInput());
        window.errors = @json((object) $errors->get('*'));
    </script>
</head>
<body>

@yield('layout')

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
