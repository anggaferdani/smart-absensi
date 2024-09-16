<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="navbar-brand navbar-brand-autodark">
      <a href="{{ route('admin.dashboard') }}">
        Smart Absensi
        {{-- <img src="{{ asset('logo.png') }}" width="150" height="" alt="" class="img-fluid"> --}}
      </a>
    </h1>
    <div class="navbar-nav flex-row d-lg-none">
      <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
          <span class="avatar avatar-sm">{{ substr(auth()->user()->email, 0, 1) }}</span>
          <div class="d-none d-xl-block ps-2">
            <div>{{ auth()->user()->email }}</div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <a href="{{ route('logout') }}" class="dropdown-item">Exit</a>
        </div>
      </div>
    </div>
    <div class="collapse navbar-collapse" id="sidebar-menu">
      <ul class="navbar-nav pt-lg-3">
        @php
          $izinPendingCount = App\Models\Izin::where('status_process', 1)->where('status_izin', 1)->where('status', 1)->count();
          $sakitPendingCount = App\Models\Izin::where('status_process', 1)->where('status_izin', 2)->where('status', 1)->count();
        @endphp
        @if(auth()->user()->role == 1)
          <li class="nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.dashboard') ? 'fw-bold' : '' }}" href="{{ route('admin.dashboard') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-house"></i></span><span class="nav-link-title">Dashboard</span></a></li>
          <li class="nav-item {{ Route::is('admin.contact-person.index') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.contact-person.index') ? 'fw-bold' : '' }}" href="{{ route('admin.contact-person.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-pen"></i></span><span class="nav-link-title">Contact Person</span></a></li>
          <li class="nav-item {{ Route::is('admin.admin.*') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.admin.*') ? 'fw-bold' : '' }}" href="{{ route('admin.admin.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-user"></i></span><span class="nav-link-title">Admin</span></a></li>
          <li class="nav-item {{ Route::is('admin.unit-kerja.*') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.unit-kerja.*') ? 'fw-bold' : '' }}" href="{{ route('admin.unit-kerja.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-layer-group"></i></span><span class="nav-link-title">Unit Kerja</span></a></li>
          <li class="nav-item {{ Route::is('admin.lokasi.*') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.lokasi.*') ? 'fw-bold' : '' }}" href="{{ route('admin.lokasi.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-location-dot"></i></span><span class="nav-link-title">Lokasi</span></a></li>
          <li class="nav-item {{ Route::is('admin.user.*') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.user.*') ? 'fw-bold' : '' }}" href="{{ route('admin.user.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-user"></i></span><span class="nav-link-title">User</span></a></li>
          <li class="nav-item {{ Route::is('admin.token') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.token') ? 'fw-bold' : '' }}" href="{{ route('admin.token') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-tag"></i></span><span class="nav-link-title">Token</span></a></li>
          <li class="nav-item {{ Route::is('admin.absen') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.absen') ? 'fw-bold' : '' }}" href="{{ route('admin.absen') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-pen"></i></span><span class="nav-link-title">Absen</span></a></li>
          <li class="nav-item {{ Route::is('admin.izin.*') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.izin.*') ? 'fw-bold' : '' }}" href="{{ route('admin.izin.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-scroll"></i></span><span class="nav-link-title">Izin</span><span class="ms-auto text-warning">Pending {{ $izinPendingCount }}</span></a></li>
          <li class="nav-item {{ Route::is('admin.sakit.*') ? 'active' : '' }}"><a class="nav-link {{ Route::is('admin.sakit.*') ? 'fw-bold' : '' }}" href="{{ route('admin.sakit.index') }}" ><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-stethoscope"></i></span><span class="nav-link-title">Sakit</span><span class="ms-auto text-warning">Pending {{ $sakitPendingCount }}</span></a></li>
        @endif
        {{-- <li class="nav-item dropdown {{ Route::is('admin.profile.*', 'admin.why-traders-choose-us.*', 'admin.experience.*', 'admin.partner.*') ? 'active' : '' }}">
          <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false"><span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fa-solid fa-folder-open"></i></span><span class="nav-link-title">Company Profile</span></a>
          <div class="dropdown-menu {{ Route::is('admin.profile.*', 'admin.why-traders-choose-us.*', 'admin.experience.*', 'admin.partner.*') ? 'show' : '' }}">
            <a class="dropdown-item {{ Route::is('admin.profile.*') ? 'fw-bold' : '' }}" href="{{ route('admin.profile.index') }}" target="" rel="noopener">Profile</a>
            <a class="dropdown-item {{ Route::is('admin.why-traders-choose-us.*') ? 'fw-bold' : '' }}" href="{{ route('admin.why-traders-choose-us.index') }}" target="" rel="noopener">Why Traders Choose Us</a>
            <a class="dropdown-item {{ Route::is('admin.experience.*') ? 'fw-bold' : '' }}" href="{{ route('admin.experience.index') }}" target="" rel="noopener">Experience</a>
            <a class="dropdown-item {{ Route::is('admin.partner.*') ? 'fw-bold' : '' }}" href="{{ route('admin.partner.index') }}" target="" rel="noopener">Partner</a>
          </div>
        </li> --}}
      </ul>
    </div>
  </div>
</aside>