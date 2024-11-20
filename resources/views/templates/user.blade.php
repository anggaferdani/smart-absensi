<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>
  <link href="{{ asset('tabler/dist/css/tabler.min.css?1692870487') }}" rel="stylesheet"/>
  <link href="{{ asset('tabler/dist/css/tabler-flags.min.css?1692870487') }}" rel="stylesheet"/>
  <link href="{{ asset('tabler/dist/css/tabler-payments.min.css?1692870487') }}" rel="stylesheet"/>
  <link href="{{ asset('tabler/dist/css/tabler-vendors.min.css?1692870487') }}" rel="stylesheet"/>
  <link href="{{ asset('tabler/dist/css/demo.min.css?1692870487') }}" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
  <style>
    @import url('https://rsms.me/inter/inter.css');
    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }
    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }
    ::-webkit-resizer{
      display: none;
    }
  </style>
</head>
<body style="background: #ffffff;">
  <header class="navbar navbar-expand-md navbar-transparent d-print-none border-bottom sticky-top bg-white" data-bs-theme="">
    <div class="container">
      <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        <a href="{{ route('user.dashboard') }}">Smart Absensi</a>
      </h1>
      <div class="navbar-nav flex-row order-md-last">
      </div>
      <a href="{{ route('logout') }}" class="btn btn-danger rounded-pill px-3 ms-auto">Exit</a>
    </div>
  </header>
  <div class="col-md-4 m-auto">
    <div class="page-wrapper">
      <div class="page-body my-0">
        <div class="container bg-blue px-0">
          @yield('header')
          @yield('dashboard')
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js?1692870487') }}" defer></script>
  <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') }}" defer></script>
  <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world.js?1692870487') }}" defer></script>
  <script src="{{ asset('tabler/dist/libs/jsvectormap/dist/maps/world-merc.js?1692870487') }}" defer></script>
  <script src="{{ asset('tabler/dist/js/tabler.min.js?1692870487') }}" defer></script>
  <script src="{{ asset('tabler/dist/js/demo.min.js?1692870487') }}" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

  <script type="text/javascript">
    $(document).ready( function () {
      $('form').on('submit', function() {
        $.LoadingOverlay("show");
    
        setTimeout(function(){
            $.LoadingOverlay("hide");
        }, 100000);
      });
    });
  </script>

  @stack('scripts')
</body>
</html>