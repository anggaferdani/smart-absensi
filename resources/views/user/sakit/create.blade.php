@extends('templates.user')
@section('title', 'Sakit')
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
  <div class="row g-2 align-items-center mb-3">
    <div class="col">
      <h2 class="page-title">Sakit</h2>
    </div>
    <div class="col-auto ms-auto">
      <div class="btn-list">
        <a href="{{ route('user.dashboard') }}" class="btn btn-success rounded-pill px-3">Home</a>
      </div>
    </div>
  </div>
  <div class="row">
    <div>
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
    </div>
  </div>
  <div class="row">
    <form action="{{ route('user.sakit.store') }}" method="POST" class="" enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label class="form-label required">Dari</label>
        <input type="date" class="form-control" name="dari" placeholder="Dari" required>
        @error('dari')<div class="text-danger">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label required">Sampai</label>
        <input type="date" class="form-control" name="sampai" placeholder="Sampai" required>
        @error('sampai')<div class="text-danger">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label required">Keterangan</label>
        <textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan" required></textarea>
        @error('keterangan')<div class="text-danger">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label required">Lampiran Surat Dokter</label>
        <input type="file" class="form-control" name="lampiran" placeholder="" required>
        @error('lampiran')<div class="text-danger">{{ $message }}</div>@enderror
      </div>
      <div class="d-flex justify-content-center gap-2 mt-5">
        <button type="submit" class="btn btn-success rounded-pill px-3">Submit</button>
        <a href="{{ route('user.sakit.index') }}" class="btn btn-success rounded-pill px-3">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection