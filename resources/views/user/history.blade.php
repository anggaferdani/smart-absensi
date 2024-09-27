@extends('templates.user')
@section('title', 'History')
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
    <div class="col-6">
      <form id="filterForm" method="GET" action="{{ route('user.history') }}">
        <select class="form-select border border-3 border-dark" name="bulan" onchange="document.getElementById('filterForm').submit();">
          <option disabled value="">Bulan</option>
          <option value="1" {{ $selectedMonth == '1' ? 'selected' : '' }}>Januari</option>
          <option value="2" {{ $selectedMonth == '2' ? 'selected' : '' }}>Februari</option>
          <option value="3" {{ $selectedMonth == '3' ? 'selected' : '' }}>Maret</option>
          <option value="4" {{ $selectedMonth == '4' ? 'selected' : '' }}>April</option>
          <option value="5" {{ $selectedMonth == '5' ? 'selected' : '' }}>Mei</option>
          <option value="6" {{ $selectedMonth == '6' ? 'selected' : '' }}>Juni</option>
          <option value="7" {{ $selectedMonth == '7' ? 'selected' : '' }}>Juli</option>
          <option value="8" {{ $selectedMonth == '8' ? 'selected' : '' }}>Agustus</option>
          <option value="9" {{ $selectedMonth == '9' ? 'selected' : '' }}>September</option>
          <option value="10" {{ $selectedMonth == '10' ? 'selected' : '' }}>Oktober</option>
          <option value="11" {{ $selectedMonth == '11' ? 'selected' : '' }}>November</option>
          <option value="12" {{ $selectedMonth == '12' ? 'selected' : '' }}>Desember</option>
        </select>
      </form>
    </div>
    <div class="col-auto ms-auto">
      <div class="btn-list">
        <a href="{{ route('user.dashboard') }}" class="btn btn-success">Home</a>
      </div>
    </div>
  </div>
  <div class="row row-cols-1 g-1 mb-3">
    <div class="col">
      <div class="card border border-3 border-dark">
        <div class="card-body p-1">
          <div class="small">Kehadiran</div>
          <div class="fs-1 fw-bold text-center">{{ $attendancePercentage }}%</div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card border border-3 border-dark">
        <div class="card-body p-1">
          <div class="small">Keterlambatan</div>
          <div class="fs-1 fw-bold text-center">{{ $lateDays }} Hari</div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card border border-3 border-dark">
        <div class="card-body p-1">
          <div class="small">Izin</div>
          <div class="fs-1 fw-bold text-center">{{ $izinDays }} Hari</div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card border border-3 border-dark">
        <div class="card-body p-1">
          <div class="small">Sakit</div>
          <div class="fs-1 fw-bold text-center">{{ $sickDays }} Hari</div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col">
      <h2 class="page-title">History</h2>
    </div>
  </div>
  <div class="row row-cards mb-3">
    @forelse($absens as $absen)
      <div>
        <div class="card">
          <div class="card-body">
            <div>Kode : {{ $absen->kode }}</div>
            <div>Tanggal : {{ $absen->tanggal }}</div>
            <div>Lokasi : {{ $absen->token->lokasi->nama }}</div>
            <div>Status : @if($absen->token->status == 1) Masuk @if($absen->status == 1) Lebih Awal @elseif($absen->status == 2) Tepat Waktu @elseif($absen->status == 3) Terlambat @endif @elseif($absen->token->status == 2) Pulang @endif</div>
            <div>Shift : @if($absen->shift == 'siang') Pagi @elseif($absen->shift == 'malam') Malam @endif</div>
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
  @include('templates.footer')
</div>
@endsection
@push('scripts')
<script>
  document.querySelector('select[name="bulan"]').addEventListener('change', function() {
      document.getElementById('filterForm').submit();
  });
</script>
@endpush