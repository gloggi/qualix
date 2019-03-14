<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<title>Qualix</title>
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
<div id="app"></div>

@yield('layout')

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
