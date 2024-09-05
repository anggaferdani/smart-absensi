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
    <div class="p-3">
      <div class="text-center fs-3 fw-bold mb-3">Anda Sudah Melakukan Absensi</div>
      <div class="text-center fs-3 fw-bold mb-1">
        @if($absen->token->status == 1) Masuk @elseif($absen->token->status == 2) Pulang @endif
      </div>
      <div class="text-center fs-1 fw-bold mb-2">
        @if($absen->status == 1) <span class="text-success">Lebih Awal</span> @elseif($absen->status == 2) <span class="text-primary">Tepat Waktu</span> @elseif($absen->status == 3) <span class="text-danger">Terlambat</span> @endif
      </div>
      <div class="text-center fs-3 fw-bold mb-2">{{ $absen->kode }}</div>
      <div class="text-center fw-bold fs-1">{{ \Carbon\Carbon::parse($absen->tanggal)->format('H:i:s') }}</div>
      <div class="text-center fw-bold mb-3">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d F Y') }}</div>
      <div class="border border-3 border-dark p-3 mb-3">
        <div class="text-center fs-3 fw-bold">Sesuai Lokasi</div>
      </div>
      <div class="text-center mb-1 fw-bold">{{ $absen->token->lokasi->nama }}</div>
      <div class="text-center mb-3">{{ $absen->token->lokasi->deskripsi }}</div>
      <div class="d-flex justify-content-center">
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary px-5 rounded-pill">Close</a>
      </div>
    </div>
  </div>
  @include('templates.footer')
</div>
@endsection