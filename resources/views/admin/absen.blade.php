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
        <button onclick="exportData('excel')" 
            class="btn btn-success btn-export"
            id="btn-excel">
            Export Excel
            <span class="spinner-border spinner-border-sm ms-2 d-none btn-spinner"></span>
        </button>

        <button onclick="exportData('pdf')" 
            class="btn btn-danger btn-export"
            id="btn-pdf">
            Export PDF
            <span class="spinner-border spinner-border-sm ms-2 d-none btn-spinner"></span>
        </button>

        <button type="button"
            class="btn btn-secondary"
            data-bs-toggle="modal"
            data-bs-target="#historyExportModal">
          History Export
        </button>
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
                <input type="text" id="dateRangePicker" class="form-control" name="date_range" value="{{ request('date_range') }}" placeholder="Dari - Sampai" autocomplete="off">
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
          <div>Status : @if($absen->token->status == 1) Masuk @if($absen->status == 1) Lebih Awal @elseif($absen->status == 2) Tepat Waktu @elseif($absen->status == 3) Terlambat @endif @elseif($absen->token->status == 2) Pulang @endif</div>
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

<div class="modal modal-blur fade" id="historyExportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="historyExportTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>File Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Download</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\ExportHistory::latest()->get() as $history)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $history->file_name }}</td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ strtoupper($history->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($history->status == 1)
                                        <span class="badge bg-danger text-white">Processing</span>
                                    @else
                                        <span class="badge bg-success text-white">Finished</span>
                                    @endif
                                </td>
                                <td>
                                    @if($history->status == 0)
                                        <a href="{{ asset('storage/'.$history->file_path) }}"
                                           class="btn btn-sm btn-primary" download>
                                            Download
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $history->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(function() {
      $('#dateRangePicker').daterangepicker({
          locale: {
              format: 'YYYY-MM-DD'
          },
          autoUpdateInput: false,
      });
  
      $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      });
  
      $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });
  });

  function exportData(type) {

      let params = new URLSearchParams(window.location.search);

      const summary = `
        <div class="mb-3 small">Pastikan filter sudah sesuai sebelum melakukan export. Export dengan jumlah data besar akan membutuhkan waktu pemrosesan lebih lama.</div>
        <b>Status Absen:</b> ${params.get('status_absen') || 'All'}<br>
        <b>Lokasi:</b> ${params.get('lokasi') || 'All'}<br>
        <b>Status Kedatangan:</b> ${params.get('status') || 'All'}<br>
        <b>Date Range:</b> ${params.get('date_range') || 'All'}<br>
        <b>Search:</b> ${params.get('search') || 'All'}
      `;

      Swal.fire({
          title: 'Yakin export data?',
          html: summary,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, export!',
      }).then((result) => {
          if (result.isConfirmed) {

              startLoadingButton();

              $.post("{{ route('admin.absen.export') }}", {
                  _token: "{{ csrf_token() }}",
                  type: type,
                  status_absen: params.get('status_absen'),
                  lokasi: params.get('lokasi'),
                  status: params.get('status'),
                  date_range: params.get('date_range'),
                  search: params.get('search'),
              }, function(res) {
                  Swal.fire('Diproses!', res.message, 'success');
              });
          }
      });
  }

  function startLoadingButton() {
      $('.btn-export').prop('disabled', true);
      $('.btn-spinner').removeClass('d-none');
  }

  function stopLoadingButton() {
      $('.btn-export').prop('disabled', false);
      $('.btn-spinner').addClass('d-none');
  }

  $(document).ready(function() {
      $('#historyExportModal').on('shown.bs.modal', function () {
          if (!$.fn.DataTable.isDataTable('#historyExportTable')) {
              $('#historyExportTable').DataTable({
                  pageLength: 10,
                  order: [[5, 'desc']]
              });
          }
      });
  });

  function checkExportStatus() {
      $.get("{{ route('admin.absen.export.status') }}", function(res) {

          if (res.processing) {
              startLoadingButton();
          } else {
              stopLoadingButton();
          }

          updateHistoryTable(res.histories);
      });
  }

  setInterval(checkExportStatus, 5000);
  checkExportStatus();

  function updateHistoryTable(histories) {

      let table = $('#historyExportTable').DataTable();
      table.clear();

      histories.forEach((item, index) => {

          let statusBadge = item.status == 1
              ? '<span class="badge bg-danger text-white">Processing</span>'
              : '<span class="badge bg-success text-white">Finished</span>';

          let downloadBtn = item.status == 0
              ? `<a href="/storage/${item.file_path}" class="btn btn-sm btn-primary" download>Download</a>`
              : '-';

          table.row.add([
              index + 1,
              item.file_name,
              `<span class="badge bg-info text-white">${item.type.toUpperCase()}</span>`,
              statusBadge,
              downloadBtn,
              new Date(item.created_at).toLocaleString()
          ]);
      });

      table.draw(false);
  }
</script>
@endpush