@extends('templates.admin')
@section('title', 'Izin')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        Izin
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
            <form action="{{ route('admin.izin.index') }}" class="">
              <div class="d-flex gap-1">
                  <select class="form-select" name="status">
                      <option disabled selected value="">Status</option>
                      <option value="">Semua</option>
                      <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Pending</option>
                      <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Approved</option>
                      <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Denied</option>
                  </select>
                  <input type="text" class="form-control" name="date_range" value="{{ request('date_range') }}" placeholder="Dari - Sampai" id="dateRangePicker" autocomplete="off">
                  <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                  <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                  <a href="{{ route('admin.izin.index') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Dari</th>
                <th>Sampai</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($izins as $izin)
                <tr>
                  <td>{{ ($izins->currentPage() - 1) * $izins->perPage() + $loop->iteration }}</td>
                  <td>{{ $izin->kode }}</td>
                  <td>{{ $izin->user->name }}</td>
                  <td>{{ \Carbon\Carbon::parse($izin->dari)->format('d-m-Y') }}</td>
                  <td>{{ \Carbon\Carbon::parse($izin->sampai)->format('d-m-Y') }}</td>
                  <td>{{ $izin->keterangan }}</td>
                  <td>
                    @if($izin->status_process == 1)
                      <span class="badge bg-blue text-blue-fg">Pending</span>
                    @elseif($izin->status_process == 2)
                      <span class="badge bg-green text-green-fg">Approved</span>
                    @elseif($izin->status_process == 3)
                      <span class="badge bg-red text-red-fg">Denied</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('admin.izin.show', $izin->kode) }}" class="btn btn-icon btn-primary"><i class="fa-solid fa-eye"></i></a>
                      @if($izin->status_process == 1)
                      <button type="button" class="btn btn-icon btn-success" data-bs-toggle="modal" data-bs-target="#approve{{ $izin->id }}"><i class="fa-solid fa-check"></i></button>
                      <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#reject{{ $izin->id }}"><i class="fa-solid fa-times"></i></button>
                      @endif
                      <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $izin->id }}"><i class="fa-solid fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex align-items-center">
          <ul class="pagination m-0 ms-auto">
            @if($izins->hasPages())
              {{ $izins->appends(request()->query())->links('pagination::bootstrap-4') }}
            @else
              <li class="page-item">No more records</li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

@foreach ($izins as $izin)
<div class="modal modal-blur fade" id="approve{{ $izin->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-success"></div>
      <form action="{{ route('admin.izin.approve', $izin->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body text-center py-4">
          <h3>Are you sure?</h3>
          <div class="text-secondary">Apakah Anda yakin ingin menyetujui ini? Tindakan ini tidak dapat diubah.</div>
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">Cancel</a></div>
              <div class="col"><button type="submit" class="btn btn-success w-100" data-bs-dismiss="modal">Approve</button></div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@foreach ($izins as $izin)
<div class="modal modal-blur fade" id="reject{{ $izin->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <form action="{{ route('admin.izin.reject', $izin->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body text-center py-4">
          <h3>Are you sure?</h3>
          <div class="text-secondary">Apakah Anda yakin ingin menyetujui ini? Tindakan ini tidak dapat diubah.</div>
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">Cancel</a></div>
              <div class="col"><button type="submit" class="btn btn-danger w-100" data-bs-dismiss="modal">Reject</button></div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@foreach ($izins as $izin)
<div class="modal modal-blur fade" id="delete{{ $izin->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <form action="{{ route('admin.izin.destroy', $izin->id) }}" method="POST">
        @csrf
        @method('Delete')
        <div class="modal-body text-center py-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
          <h3>Are you sure?</h3>
          <div class="text-secondary">Are you sure you want to delete this? This action cannot be undone.</div>
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">Cancel</a></div>
              <div class="col"><button type="submit" class="btn btn-danger w-100" data-bs-dismiss="modal">Delete</button></div>
            </div>
          </div>
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
                            @foreach(\App\Models\ExportHistory::where('file_name', 'like', 'izin-%')->latest()->get() as $history)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $history->file_name }}</td>
                                <td><span class="badge bg-info text-white">{{ strtoupper($history->type) }}</span></td>
                                <td>
                                    @if($history->status == 1)
                                        <span class="badge bg-danger text-white">Processing</span>
                                    @else
                                        <span class="badge bg-success text-white">Finished</span>
                                    @endif
                                </td>
                                <td>
                                    @if($history->status == 0)
                                        <a href="{{ asset('storage/'.$history->file_path) }}" class="btn btn-sm btn-primary" download>Download</a>
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
          locale: { format: 'YYYY-MM-DD' },
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
        <b>Status:</b> ${params.get('status') || 'All'}<br>
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

              $.post("{{ route('admin.izin.export') }}", {
                  _token: "{{ csrf_token() }}",
                  type: type,
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
      $.get("{{ route('admin.izin.export.status') }}", function(res) {
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