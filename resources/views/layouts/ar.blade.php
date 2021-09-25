<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="app-url" content="{{ env('APP_URL') }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    {{-- Font Awesome Free 5.8.2, Bootstrap v4.5.3 and Custom Style --}}
    <link href="{{ asset('css/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/ar/ar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    {{-- aframe 1.0.4 and aframe-ar 3.3.1 --}}
    <script src="{{ asset('js/ar/aframe.js') }}"></script>
    <script src="{{ asset('js/ar/aframe-ar.js') }}"></script>
    <script src="{{ asset('js/ar/aframe-components.js') }}"></script>
    
    @stack('head_css')
    
</head>
<body class="ar-body">
  @yield('content')
  <script src="{{ asset('js/ar/app.js') }}"></script>
</body>
</html>
