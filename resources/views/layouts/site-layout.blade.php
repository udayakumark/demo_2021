<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
      <meta name="description" content="">
      <meta name="author" content="">
      <meta name="token" content="{{ csrf_token() }}" />
      <link rel="icon" type="image/png" href="{{ asset('public/site-assets/images/favicon.png') }}">
      <title>@yield('title')</title>
      <!-- Styles -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/bootstrap.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/ionicons.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/fontawesome.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/slick.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/animate.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/jquery-ui.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/default.css') }}">
      <link rel="stylesheet" href="{{ asset('public/site-assets/datatables/datatables.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/plugins/sweetalert2.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/site-assets/css/style.css') }}">
      <!-- Script Functions -->
      <script src="{{ asset('public/site-assets/js/vendor/jquery-1.12.4.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/vendor/modernizr-3.7.1.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/plugins/popper.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/plugins/bootstrap.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/plugins/slick.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/plugins/jquery.countdown.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/plugins/jquery-ui.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/plugins/sweetalert2.min.js') }}" defer></script>
          <script src="{{ asset('public/site-assets/datatables/datatables.min.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/js/main.js') }}" defer></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQ5y0EF8dE6qwc03FcbXHJfXr4vEa7z54" defer></script>
      <script src="{{ asset('public/site-assets/js/map-script.js') }}" defer></script>
      <script src="{{ asset('public/site-assets/pages/common.js') }}" defer></script>
      <script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
      <script src="@yield('script-src')" defer></script>
    </head>
    
    <body>
      <!--====== PRELOADER PART START ======-->
      <div id="preloader">
        <div class="preloader">
          <span></span>
          <span></span>
        </div>
      </div>
      <!--====== PRELOADER PART ENDS ======-->
      @include('partials.site-header')
      @yield('content')
      @include('partials.site-footer')
    </body>
    @yield('script')
  </html>