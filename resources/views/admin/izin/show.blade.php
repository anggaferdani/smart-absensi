@extends('templates.admin')
@section('title', 'Izin')
@section('content')
<div class="container-xl">
  <div class="row">
    <div class="col-8 m-auto">
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
          <div class="d-flex align-items-center mb-5">
            <div class="" style="position: absolute; transform: translate(40px, 0px);"><img src="{{ asset('images/logo2.png') }}" alt="" class="" width="70"></div>
            <div class="m-auto">
              <div class="fw-bold text-center fs-3">PT GUNA CIPTA KREASI</div>
              <div class="fw-bold text-center">Jalan Lapangan Bola No. 7 Kebon Jeruk Jakarta Barat</div>
            </div>
          </div>
          <hr>
          <div class="text-center fs-3 fw-bold mb-5">FORMULIR PENGAJUAN IZIN KARYAWAN</div>
          <div class="mb-3">Kepada YTH</div>
          <div>HRD PT Guna Cipta Kreasi</div>
          <div>di</div>
          <div class="mb-3">Tempat</div>
          <div class="mb-3">Perihal : <span class="fw-bold">{{ $izin->keterangan }}</span></div>
          <div class="mb-3">Saya yang bertanda tangan dibawah ini :</div>
          <table class="mb-3 w-100">
            <tr>
              <td style="width: 20%;">Nama</td>
              <td>: <span class="fw-bold">{{ $izin->user->name }}</span></td>
            </tr>
            <tr>
              <td style="width: 20%;">Jabatan</td>
              <td>: <span class="fw-bold">{{ $izin->user->jabatan }}</span></td>
            </tr>
            <tr>
              <td style="width: 20%;">Unit Kerja</td>
              <td>: {{ $izin->user->unitKerja->nama ?? '-' }}</td>
            </tr>
            <tr>
              <td style="width: 20%;">Lokasi Kerja</td>
              <td>: {{ $izin->user->lokasi->nama }}, {{ $izin->user->lokasi->deskripsi }}</td>
            </tr>
          </table>
          <div class="mb-3">Bermaksud untuk mengajukan permohonan izin selama {{ \Carbon\Carbon::parse($izin->dari)->diffInDays(\Carbon\Carbon::parse($izin->sampai)) + 1 }} hari dari tanggal <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->dari)->translatedFormat('d') }}</span> bulan <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->dari)->translatedFormat('F') }}</span> tahun <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->dari)->translatedFormat('Y') }}</span> sampai dengan tanggal <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->sampai)->translatedFormat('d') }}</span> bulan <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->sampai)->translatedFormat('F') }}</span> tahun <span class="fw-bold">{{ \Carbon\Carbon::parse($izin->sampai)->translatedFormat('Y') }}</span>.</div>
          <div class="mb-3">Demikian surat permohonan ini saya buat. Atas perhatian nya saya ucapkan terima kasih.</div>
          <div class="mb-3"><span class="fw-bold">{{ \Carbon\Carbon::parse($izin->sampai)->locale('id')->translatedFormat('l, d F Y') }}</span></div>
          <div class="mb-3">Hormat Saya,</div>
          <div class="mb-5"><span class="fw-bold">{{ $izin->user->name }}</span></div>
          <div class="d-flex gap-3">
            @if($izin->lampiran)
              <div>
                <div class="mb-2">Lampiran :</div>
                <img src="/izin/{{ $izin->lampiran }}" alt="" width="250">
              </div>
            @endif
          </div>
        </div>
        <div class="card-footer">
          <a href="{{ route('admin.izin.index') }}" class="btn btn-primary">Back</a>
          @if($izin->status_process == 1)
          <button type="button" class="btn btn-icon btn-success" data-bs-toggle="modal" data-bs-target="#approve{{ $izin->id }}"><i class="fa-solid fa-check"></i></button>
          <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#reject{{ $izin->id }}"><i class="fa-solid fa-times"></i></button>
          @endif
          <a href="{{ route('admin.izin.show', array_merge(request()->query(), ['izin' => $izin->kode, 'export' => 'pdf'])) }}" class="btn btn-secondary" target="_blank">Print Dokumen</a>
        </div>
      </div>
    </div>
  </div>
</div>

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
@endsection