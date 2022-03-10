<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('public/admin-assets/bundles/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin-assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin-assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin-assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin-assets/css/style.css') }}">
    <link rel='shortcut icon' type='image/x-icon' href="{{ asset('public/admin-assets/img/favicon.ico') }}" />

    <!-- Scripts -->
    <script src="{{ asset('public/admin-assets/js/app.min.js') }}" defer></script>
    <script src="{{ asset('public/admin-assets/js/custom.js') }}" defer></script>
    <script src="{{ asset('public/admin-assets/js/scripts.js') }}" defer></script>

</head>
<body>
<div class="loader"></div>
  <div id="app">
  <section class="section">
        @yield('content')
    </section>
  </div>
</body>
</html>
