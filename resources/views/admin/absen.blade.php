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
        {{-- Tombol sekarang memicu modal konfirmasi, bukan langsung export --}}
        <button type="button" class="btn btn-success {{ request('tanggal') ? 'disabled' : '' }}"
                onclick="openExportModal('excel')">
          Export Excel
        </button>
        <button type="button" class="btn btn-danger {{ request('tanggal') ? 'disabled' : '' }}"
                onclick="openExportModal('pdf')">
          Export PDF
        </button>
        <button type="button" class="btn btn-secondary {{ request('tanggal') ? 'disabled' : '' }}"
                onclick="openExportModal('print')">
          Print Laporan
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
            <form action="{{ route('admin.absen') }}" class="" id="filterForm">
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

{{-- ================================================================
     MODAL DETAIL ABSEN (tidak diubah)
     ================================================================ --}}
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

@endsection

@push('scripts')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  /* ===== DATE RANGE PICKER ===== */
  $(function () {
    $('#dateRangePicker').daterangepicker({
      locale: { format: 'YYYY-MM-DD' },
      autoUpdateInput: false,
    });
    $('#dateRangePicker').on('apply.daterangepicker', function (ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('#dateRangePicker').on('cancel.daterangepicker', function () {
      $(this).val('');
    });
  });

  /* ===== CONSTANTS ===== */
  const STATUS_ABSEN_LABEL = { '': 'Semua', '1': 'Masuk', '2': 'Pulang' };
  const STATUS_LABEL       = { '': 'Semua', '1': 'Lebih Awal', '2': 'Tepat Waktu', '3': 'Terlambat' };
  const TYPE_LABEL         = { excel: 'Excel (.xlsx)', pdf: 'PDF (.pdf)', print: 'Print (PDF inline)' };
  const TYPE_COLOR         = { excel: '#2fb344', pdf: '#d63939', print: '#626976' };
  const LS_KEY             = 'absen_export_history';

  /* ===== EXPORT HISTORY ===== */
  function saveExportHistory(entry) {
    let history = [];
    try { history = JSON.parse(localStorage.getItem(LS_KEY) || '[]'); } catch (e) {}
    history.unshift(entry);
    if (history.length > 50) history = history.slice(0, 50);
    localStorage.setItem(LS_KEY, JSON.stringify(history));
  }

  /* ===== FILTERS ===== */
  function getCurrentFilters() {
    const p = new URLSearchParams(window.location.search);
    const lokasi = p.get('lokasi') || '';
    let lokasiNama = 'Semua';
    if (lokasi) {
      const opt = document.querySelector(`select[name="lokasi"] option[value="${lokasi}"]`);
      lokasiNama = opt ? opt.textContent.trim() : lokasi;
    }
    return {
      dateRange:   p.get('date_range')   || '',
      search:      p.get('search')       || '',
      lokasi,
      lokasiNama,
      statusAbsen: p.get('status_absen') || '',
      status:      p.get('status')       || '',
    };
  }

  /* ===== CONFIRMATION HTML (left-aligned, bold values, no badge) ===== */
  function buildFilterTable(type) {
    const f = getCurrentFilters();
    const rows = [
      ['Tipe Export',       `<b>${TYPE_LABEL[type]}</b>`],
      ['Tanggal',           f.dateRange   ? `<b>${f.dateRange}</b>`   : '<span style="color:#aaa">All</span>'],
      ['Pencarian',         f.search      ? `<b>${f.search}</b>`      : '<span style="color:#aaa">All</span>'],
      ['Lokasi',            f.lokasi      ? `<b>${f.lokasiNama}</b>`  : '<span style="color:#aaa">All</span>'],
      ['Status Absen',      STATUS_ABSEN_LABEL[f.statusAbsen] && f.statusAbsen
                              ? `<b>${STATUS_ABSEN_LABEL[f.statusAbsen]}</b>`
                              : '<span style="color:#aaa">All</span>'],
      ['Status Kedatangan', STATUS_LABEL[f.status] && f.status
                              ? `<b>${STATUS_LABEL[f.status]}</b>`
                              : '<span style="color:#aaa">All</span>'],
    ];
    return `
      <p style="color:#6c757d;margin:0 0 10px;font-size:13px;text-align:left">
        Data yang akan diekspor:
      </p>
      <table style="width:100%;font-size:13px;border-collapse:collapse;text-align:left">
        ${rows.map(([l, v]) => `
          <tr>
            <td style="padding:4px 14px 4px 0;color:#6c757d;white-space:nowrap;vertical-align:middle">${l}</td>
            <td style="padding:4px 0;vertical-align:middle">${v}</td>
          </tr>`).join('')}
      </table>`;
  }

  /* ===== BUTTON HELPERS ===== */
  function setButtonLoading(btn) {
    btn.disabled = true;
    btn.dataset.originalHtml = btn.innerHTML;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span>${btn.textContent.trim()}`;
  }

  function setButtonDone(btn) {
    btn.disabled = false;
    if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
  }

  /* ===== STEP 1: KONFIRMASI ===== */
  function openExportModal(type) {
    Swal.fire({
      title: `Konfirmasi Export ${TYPE_LABEL[type]}`,
      html:  buildFilterTable(type),
      icon:  'question',
      showCancelButton:   true,
      confirmButtonText:  '<i class="fa-solid fa-file-export me-1"></i> Ya, Export',
      cancelButtonText:   'Batal',
      confirmButtonColor: TYPE_COLOR[type],
      cancelButtonColor:  '#6c757d',
      reverseButtons: true,
      width: 460,
      customClass: { htmlContainer: 'text-start' },
    }).then((result) => {
      if (result.isConfirmed) {
        const btnMap = { excel: 0, pdf: 1, print: 2 };
        const btns = document.querySelectorAll('.btn-list button');
        const activeBtn = btns[btnMap[type]] || null;
        if (activeBtn) setButtonLoading(activeBtn);
        dispatchExportJob(type, activeBtn);
      }
    });
  }

  /* ===== STEP 2: DISPATCH JOB ===== */
  function dispatchExportJob(type, btn) {
    const filters = getCurrentFilters();

    $.ajax({
      url:    '{{ route("admin.absen.export.dispatch") }}',
      method: 'POST',
      data: {
        _token:       '{{ csrf_token() }}',
        type,
        date_range:   filters.dateRange,
        search:       filters.search,
        lokasi:       filters.lokasi,
        status_absen: filters.statusAbsen,
        status:       filters.status,
      },
      success: function (response) {
        if (response.success) {
          startPolling(response.job_key, type, btn, filters);
        } else {
          if (btn) setButtonDone(btn);
          const failTitles = { excel: 'Export Excel Gagal', pdf: 'Export PDF Gagal', print: 'Print Laporan Gagal' };
          showToast('error', failTitles[type] ?? 'Export Gagal', 'Gagal memulai export.');
        }
      },
      error: function (xhr) {
        if (btn) setButtonDone(btn);
        const failTitles = { excel: 'Export Excel Gagal', pdf: 'Export PDF Gagal', print: 'Print Laporan Gagal' };
        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat memulai export.';
        showToast('error', failTitles[type] ?? 'Export Gagal', msg);
      },
    });
  }

  /* ===== STEP 3: POLLING ===== */
  function startPolling(jobKey, type, btn, filters) {
    const statusUrl = '{{ route("admin.absen.export.status", ["key" => "__KEY__"]) }}'
                        .replace('__KEY__', jobKey);

    // URL untuk hapus file saat toast di-close
    const deleteUrl = '{{ route("admin.absen.export.destroy", ["key" => "__KEY__"]) }}'
                        .replace('__KEY__', jobKey);

    // Judul toast berbeda per tipe export
    const successTitles = {
      excel: 'Export Excel Selesai!',
      pdf:   'Export PDF Selesai!',
      print: 'Print Laporan Selesai!',
    };
    const failTitles = {
      excel: 'Export Excel Gagal',
      pdf:   'Export PDF Gagal',
      print: 'Print Laporan Gagal',
    };

    const interval = setInterval(function () {
      $.ajax({
        url: statusUrl,
        method: 'GET',
        success: function (data) {
          if (data.status === 'completed') {
            clearInterval(interval);
            if (btn) setButtonDone(btn);

            saveExportHistory({
              type,
              label:       TYPE_LABEL[type],
              filters,
              downloadUrl: data.download_url,
              filename:    data.filename || '',
              at:          new Date().toISOString(),
              status:      'completed',
            });

            showToast(
              'success',
              successTitles[type] ?? 'Export Selesai!',
              `File <b>${data.filename || TYPE_LABEL[type]}</b> siap diunduh.`,
              data.download_url,
              deleteUrl
            );

          } else if (data.status === 'failed') {
            clearInterval(interval);
            if (btn) setButtonDone(btn);

            saveExportHistory({
              type,
              label:   TYPE_LABEL[type],
              filters,
              at:      new Date().toISOString(),
              status:  'failed',
              message: data.message || 'Export gagal.',
            });

            showToast(
              'error',
              failTitles[type] ?? 'Export Gagal',
              data.message || 'Export gagal.',
              null,
              null
            );
          }
          // pending / processing → lanjut polling
        },
        error: function () {
          clearInterval(interval);
          if (btn) setButtonDone(btn);
          showToast('error', failTitles[type] ?? 'Export Gagal', 'Gagal mengecek status export.');
        },
      });
    }, 2000);
  }
</script>
@endpush