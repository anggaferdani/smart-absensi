@extends('templates.authentications')
@section('title', 'Login')
@section('content')
<div class="container container-tight py-4">
  <div class="text-center mb-4">
    <a href="{{ route('index') }}" class="navbar-brand navbar-brand-autodark">
      <img src="{{ asset('logo.png') }}" width="200" height="" alt="" class="">
    </a>
  </div>
  <div class="card card-md">
    <div class="card-body">
      <h2 class="h2 text-center mb-4">Login</h2>
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
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" placeholder="Email">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" placeholder="Password">
        </div>
        <div class="form-footer">
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection