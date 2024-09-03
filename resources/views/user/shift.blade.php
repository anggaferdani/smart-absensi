@extends('templates.user')
@section('title', 'Izin')
@section('content')
<div class="row">
  <div class="bg-blue d-flex align-items-center p-1">
    <div class="d-flex justify-content-center p-3"><img src="/profile-picture/{{ auth()->user()->profile_picture }}" alt="" class="rounded-circle border border-dark border-3" width="70"></div>
    <div>
      <div class="text-white fs-1">{{ auth()->user()->name }}</div>
      <div class="text-white fs-3">{{ auth()->user()->jabatan }}</div>
    </div>
  </div>
</div>
<div class="row">
  <div class="p-3 mb-3">
    <div id="time" class="text-center fw-bold fs-1"></div>
    <div id="date" class="text-center fw-bold"></div>
  </div>
  <div class="col-3 m-auto">
    <div class="row g-3">
      <div class="col-12">
        <a href="{{ route('user.index', ['shift' => 'siang']) }}">
          <div class="border border-3 border-dark rounded-4 p-2">
            <img src="{{ asset('images/sun.png') }}" alt="">
          </div>
        </a>
        <div class="text-dark text-dacoration-none text-center">Absen Shift Siang</div>
      </div>
      <div class="col-12">
        <a href="{{ route('user.index', ['shift' => 'malam']) }}">
          <div class="border border-3 border-dark rounded-4 p-2">
            <img src="{{ asset('images/moon.png') }}" alt="">
          </div>
        </a>
        <div class="text-dark text-dacoration-none text-center">Absen Shift Malam</div>
      </div>
    </div>
  </div>
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
      document.getElementById('time').innerHTML = timeString;
      document.getElementById('date').innerHTML = dateString;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>
@endpush