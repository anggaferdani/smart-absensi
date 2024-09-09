@extends('templates.admin')
@section('title', 'Lokasi')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        Preview Lokasi {{ $lokasi->nama }}
      </h2>
    </div>
    <div class="col-auto ms-auto d-print-none">
      <div class="btn-list">
        <a href="{{ route('admin.lokasi.index') }}" class="btn btn-primary">Back</a>
      </div>
    </div>
  </div>
</div>
@endsection
@section('content')
<div class="container-xl">
  <div class="row">
    <div class="col-12">
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
      <div class="card">
        <div class="card-body">
          <div id="map" style="height: 400px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  var map = L.map('map').setView([{{ $lokasi->lat }}, {{ $lokasi->long }}], 50);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

  var popupContent = `
    <div style="text-align: center;">
      <div class="fw-bold fs-5 mb-2">{{ $lokasi->nama }}</div>
      <div>{{ $lokasi->deskripsi }}</div>
    </div>`;

  L.marker([{{ $lokasi->lat }}, {{ $lokasi->long }}])
    .addTo(map)
    .bindPopup(popupContent)
    .openPopup();
  
  L.circle([{{ $lokasi->lat }}, {{ $lokasi->long }}], {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: {{ $lokasi->radius }}
  }).addTo(map);
</script>
@endpush