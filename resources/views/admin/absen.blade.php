@extends('templates.admin')
@section('title', 'Absen')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        Absen
      </h2>
    </div>
    <div class="col-auto ms-auto">
      <div class="btn-list">
        <a href="{{ route('admin.absen', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-success {{ request('tanggal') ? 'disabled' : '' }}">Export Excel</a>
        <a href="{{ route('admin.absen', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-danger {{ request('tanggal') ? 'disabled' : '' }}">Export PDF</a>
        <a href="{{ route('admin.absen', array_merge(request()->query(), ['export' => 'print'])) }}" class="btn btn-secondary {{ request('tanggal') ? 'disabled' : '' }}" target="_blank">Print Laporan</a>
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
        <div class="card-header">
          <div class="ms-auto">
            <form action="{{ route('admin.absen') }}" class="">
              <div class="d-flex gap-1">
                <select class="form-select" name="status_absen">
                  <option disabled selected value="">Status</option>
                  <option value="">Semua</option>
                  <option value="1" {{ request('status_absen') == '1' ? 'selected' : '' }}>Masuk</option>
                  <option value="2" {{ request('status_absen') == '2' ? 'selected' : '' }}>Pulang</option>
                </select>
                <select class="form-select" name="lokasi">
                    <option disabled selected value="">Lokasi</option>
                    <option value="">Semua</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select" name="status">
                    <option disabled selected value="">Status Kedatangan</option>
                    <option value="">Semua</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Lebih Awal</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Terlambat</option>
                </select>
                <input id="inputBulan" type="month" class="form-control" name="bulan" value="{{ request('bulan') }}" placeholder="" {{ request('date') ? 'disabled' : '' }}>
                <input id="inputTanggal" type="date" class="form-control" name="tanggal" value="{{ request('tanggal') }}" placeholder="" {{ request('bulan') ? 'disabled' : '' }}>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="{{ route('admin.absen') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Status Absen</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($absens as $absen)
                <tr>
                  <td>{{ ($absens->currentPage() - 1) * $absens->perPage() + $loop->iteration }}</td>
                  <td>{{ $absen->user->name }}</td>
                  <td>{{ $absen->kode }}</td>
                  <td>{{ $absen->tanggal }}</td>
                  <td>
                    @if($absen->shift == 'siang')
                      <span class="badge bg-yellow text-yellow-fg">Pagi</span>
                    @elseif($absen->shift == 'malam')
                      <span class="badge bg-dark text-dark-fg">Malam</span>
                    @endif
                  </td>
                  <td>
                    @if($absen->token->status == 1)
                      <span class="badge bg-blue text-blue-fg">Masuk</span>
                    @elseif($absen->token->status == 2)
                      <span class="badge bg-red text-red-fg">Pulang</span>
                    @endif
                  </td>
                  <td>
                    @if($absen->token->status == 1)
                      @if($absen->status == 1)
                        <span class="badge bg-blue text-blue-fg">Lebih Awal</span>
                      @elseif($absen->status == 2)
                        <span class="badge bg-green text-green-fg">Tepat Waktu</span>
                      @elseif($absen->status == 3)
                        <span class="badge bg-red text-red-fg">Terlambat</span>
                      @endif
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#show{{ $absen->id }}"><i class="fa-solid fa-eye"></i></button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex align-items-center">
          <ul class="pagination m-0 ms-auto">
            @if($absens->hasPages())
              {{ $absens->appends(request()->query())->links('pagination::bootstrap-4') }}
            @else
              <li class="page-item">No more records</li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

@foreach ($absens as $absen)
<div class="modal modal-blur fade" id="show{{ $absen->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="" method="POST" class="">
        <div class="modal-header">
          <h5 class="modal-title">Kode {{ $absen->kode }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div>Token : {{ $absen->kode }}</div>
          <div>Token : {{ $absen->token->token }}</div>
          <div>Lokasi : {{ $absen->token->lokasi->nama }}</div>
          <div>Status : @if($absen->token->status == 1) <span class="badge bg-blue text-blue-fg">Masuk</span> @elseif($absen->token->status == 2) <span class="badge bg-red text-red-fg">Pulang</span> @endif</div>
          <div>Nama : {{ $absen->user->name }}</div>
          <div>Email : {{ $absen->user->email }}</div>
          <div>Tanggal : {{ $absen->tanggal }}</div>
          <div>Status @if($absen->token->status == 1) Masuk @elseif($absen->token->status == 2) Pulang @endif : @if($absen->status == 1) Lebih Awal @elseif($absen->status == 2) Tepat Waktu @elseif($absen->status == 3) Terlambat @endif</div>
          <div>Lat : {{ $absen->lat }}</div>
          <div>Long : {{ $absen->long }}</div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endsection
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const inputBulan = document.getElementById('inputBulan');
    const inputTanggal = document.getElementById('inputTanggal');

    function updateInputState() {
      if (inputBulan.value) {
        inputTanggal.disabled = true;
      } else {
        inputTanggal.disabled = false;
      }

      if (inputTanggal.value) {
        inputBulan.disabled = true;
      } else {
        inputBulan.disabled = false;
      }
    }

    inputBulan.addEventListener('input', updateInputState);

    inputTanggal.addEventListener('input', updateInputState);

    updateInputState();
  });
</script>
@endpush