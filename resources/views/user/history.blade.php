@extends('templates.user')
@section('title', 'History')
@section('content')
<div class="row">
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
<div class="row mb-3">
  <h2 class="page-title">History</h2>
</div>
<div class="row row-cards mb-3">
  @forelse($absens as $absen)
    <div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Tanggal {{ $absen->tanggal }}</h3>
        </div>
        <div class="card-body">
          <div>Token : {{ $absen->token->token }}</div>
          <div>Lokasi : {{ $absen->token->lokasi->nama }}</div>
          <div>Status : @if($absen->token->status == 1) <span class="badge bg-blue text-blue-fg">Masuk</span> @elseif($absen->token->status == 2) <span class="badge bg-red text-red-fg">Pulang</span> @endif</div>
          <div>Kode : {{ $absen->kode }}</div>
          <div>Nama : {{ $absen->user->name }}</div>
          <div>Email : {{ $absen->user->email }}</div>
          <div>Status @if($absen->token->status == 1) Masuk @elseif($absen->token->status == 2) Pulang @endif : @if($absen->status == 1) Lebih Awal @elseif($absen->status == 2) Tepat Waktu @elseif($absen->status == 3) Terlambat @endif</div>
          <div>Lat : {{ $absen->lat }}</div>
          <div>Long : {{ $absen->long }}</div>
        </div>
      </div>
    </div>
  @empty
    <div><img src="{{ asset('images/bloom-a-man-looks-at-a-blank-sheet-of-paper-in-puzzlement.png') }}" alt="" class="img-fluid m-auto"></div>
  @endforelse
</div>
<div class="d-flex justify-content-center">
  <ul class="pagination m-0">
    @if($absens->hasPages())
      {{ $absens->appends(request()->query())->links('pagination::bootstrap-4') }}
    @else
      <li class="page-item">No more records</li>
    @endif
  </ul>
</div>
@endsection