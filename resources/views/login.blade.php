@extends('templates.authentications')
@section('title', 'Login')
@section('content')
<div class="container container-tight py-4">
  <div class="card card-md border-0">
    <div class="card-body py-0">
      <div id="datetime" class="text-center fw-bold fs-2 mb-5"></div>
      <img src="{{ asset('images/login.jpeg') }}" alt="" class="mb-2">
      <h1 class="text-center mb-4">Selamat Datang</h1>
      @if(Session::get('success'))
        <div class="alert alert-important alert-success" role="alert">
          {{ Session::get('success') }}
        </div>
      @endif
      @if(Session::get('error'))
        <div class="alert alert-important alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
      @endif
      <form action="{{ route('post.login') }}" method="post">
        @csrf
        <div class="mb-1">
          <input type="text" class="form-control rounded-pill px-3 border border-3 border-dark" name="login" placeholder="Email atau No. HP">
        </div>
        <div class="">
          <input type="password" class="form-control rounded-pill px-3 border border-3 border-dark" name="password" placeholder="Password">
        </div>
        <div class="form-footer">
          <button type="submit" class="btn btn-primary w-100 rounded-pill">Login</button>
        </div>
      </form>
      <div class="text-center mt-5"><img src="{{ asset('images/logo.png') }}" alt="" class="img-fluid" width="200"></div>
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
      document.getElementById('datetime').innerHTML = fullDateTime;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>
@endpush