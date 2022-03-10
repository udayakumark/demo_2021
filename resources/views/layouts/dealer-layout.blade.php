<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="token" content="{{ csrf_token() }}" />
    <title>@yield('title')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/css/development.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/bundles/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/bundles/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dealer-assets/bundles/izitoast/css/iziToast.min.css') }}">
    <link rel='shortcut icon' type='image/x-icon' href="{{ asset('public/dealer-assets/img/favicon.ico') }}" />

    <!-- Scripts -->
    <script src="{{ asset('public/dealer-assets/js/app.min.js') }}" ></script>
    <script src="{{ asset('public/dealer-assets/js/custom.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/js/scripts.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/apexcharts/apexcharts.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/js/page/index.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/datatables.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/export-tables/dataTables.buttons.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/export-tables/buttons.flash.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/export-tables/jszip.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/export-tables/pdfmake.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/export-tables/vfs_fonts.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/datatables/export-tables/buttons.print.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/js/page/datatables.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/izitoast/js/iziToast.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/js/page/toastr.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/sweetalert/sweetalert.min.js') }}" defer></script>
    <script src="{{ asset('public/dealer-assets/bundles/select2/dist/js/select2.full.min.js') }}" defer></script>
    <script src="@yield('script-src')" defer></script>
</head>
<body>
<div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      @include('partials.dealer-header')
      @include('partials.dealer-menus')
      <!-- Main Content -->
      @yield('content')
      @include('partials.dealer-footer')
      @include('partials.alert-message')
    </div>
  </div>
</body>


<!-- Script Functions -->
<script type="text/javascript">

  $.ajaxSetup({
    headers:{
      'X-CSRF-TOKEN' : $('meta[name="token"]').attr('content')
    }
  });

  // Loader when ajax calls
  $(document).bind("ajaxSend", function(){
    $(".loader").show();
  }).bind("ajaxComplete", function(){
    $(".loader").hide();
  });

</script>
@yield('script')
</html>
