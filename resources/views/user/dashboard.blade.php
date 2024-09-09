@extends('templates.user')
@section('title', 'Izin')
@section('header')
<div class="row">
  <div class="p-3">
    <div class="d-flex justify-content-center p-3"><img src="/profile-picture/{{ auth()->user()->profile_picture }}" alt="" class="rounded-circle border border-dark border-3" width="150"></div>
    <div class="text-center text-white fs-1">{{ auth()->user()->name }}</div>
    <div class="text-center text-white fs-3">{{ auth()->user()->jabatan }}</div>
  </div>
</div>
@endsection
@section('dashboard')
<div class="bg-white">
  <div class="row">
    <div class="p-3">
      <div id="datetime" class="text-center fw-bold bg-yellow rounded-pill border border-dark border-3 p-1 fs-2"></div>
    </div>
    <div class="col-6 m-auto">
      <div class="row g-3">
        <div class="col-6">
          <a href="{{ route('user.shift') }}">
            <div class="border border-3 border-dark rounded-4 p-2">
              <img src="{{ asset('images/absen.png') }}" alt="">
            </div>
          </a>
          <div class="text-dark text-dacoration-none text-center">Absen Kehadiran</div>
        </div>
        <div class="col-6">
          <a href="{{ route('user.history') }}">
            <div class="border border-3 border-dark rounded-4 p-2">
              <img src="{{ asset('images/history.png') }}" alt="">
            </div>
          </a>
          <div class="text-dark text-dacoration-none text-center">Riwayat Absensi</div>
        </div>
        <div class="col-6">
          <a href="{{ route('user.izin.index') }}">
            <div class="border border-3 border-dark rounded-4 p-2">
              <img src="{{ asset('images/izin.png') }}" alt="">
            </div>
          </a>
          <div class="text-dark text-dacoration-none text-center">Izin</div>
        </div>
        <div class="col-6">
          <a href="{{ route('user.sakit.index') }}">
            <div class="border border-3 border-dark rounded-4 p-2">
              <img src="{{ asset('images/sakit.png') }}" alt="">
            </div>
          </a>
          <div class="text-dark text-dacoration-none text-center">Sakit</div>
        </div>
      </div>
    </div>
  </div>
  @include('templates.footer')
</div>
@endsection
@push('scripts')
<script>
  function updateDateTime() {
      const now = new Date();
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const dateString = now.toLocaleDateString('id-ID', options);
      const timeString = now.toLocaleTimeString('en-GB', { hour12: false });
      const fullDateTime = `${dateString} ${timeString}`;
      document.getElementById('datetime').innerHTML = fullDateTime;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>
@endpush