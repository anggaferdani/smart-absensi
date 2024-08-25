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
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
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
        <a href="{{ route('user.index') }}">Smart Absensi</a>
      </h1>
      <div class="navbar-nav flex-row order-md-last">
      </div>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav ms-md-auto">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('user.index') }}">Index</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('user.history') }}">History</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button">
                <span class="nav-link-title">{{ auth()->user()->email }}</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('user.izin.index') }}">Izin</a>
                <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <div class="col-md-4 m-auto">
    <div class="page-wrapper">
      <div class="page-body">
        <div class="container">
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
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  @stack('scripts')
</body>
</html>