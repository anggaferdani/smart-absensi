@extends('templates.user')
@section('title', 'Izin')
@section('header')
<div class="row">
  <div class="d-flex align-items-center">
    <div class="d-flex justify-content-center p-3"><img src="/profile-picture/{{ auth()->user()->profile_picture }}" alt="" class="rounded-circle border border-dark border-3" width="70"></div>
    <div>
      <div class="text-white fs-1">{{ auth()->user()->name }}</div>
      <div class="text-white fs-3">{{ auth()->user()->jabatan }}</div>
    </div>
  </div>
</div>
@endsection
@section('content')
<div style="border-radius: 70px; border-bottom-left-radius: 0; border-bottom-right-radius: 0;" class="bg-white p-0 px-5 py-5 vh-100">
  <div class="row">
    <div class="p-3 mb-3">
      <div id="time" class="text-center fw-bold fs-1"></div>
      <div id="date" class="text-center fw-bold fs-1"></div>
    </div>
    <div class="col-3 m-auto">
      <div class="row g-3">
        <div class="col-12">
          <a href="{{ route('user.index', ['shift' => 'siang']) }}">
            <div class="border border-3 border-dark rounded-4 p-2 mb-2">
              <img src="{{ asset('images/siang.jpeg') }}" alt="">
            </div>
          </a>
          <div class="text-dark fw-bold fs-3 lh-1 text-dacoration-none text-center">ABSEN MASUK PAGI</div>
        </div>
        <div class="col-12">
          <a href="{{ route('user.index', ['shift' => 'malam']) }}">
            <div class="border border-3 border-dark rounded-4 p-2 mb-2">
              <img src="{{ asset('images/malam.jpeg') }}" alt="">
            </div>
          </a>
          <div class="text-dark fw-bold fs-3 lh-1 text-dacoration-none text-center">ABSEN MASUK MALAM</div>
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
      document.getElementById('time').innerHTML = timeString;
      document.getElementById('date').innerHTML = dateString;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>
@endpush