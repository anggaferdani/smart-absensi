@extends('templates.admin')
@section('title', 'Sakit')
@section('content')
<div class="container-xl">
  <div class="row">
    <div class="col-6 m-auto">
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
          <h3 class="card-title">Kode Sakit {{ $izin->kode }}</h3>
        </div>
        <div class="card-body">
          <div>Nama : {{ $izin->user->name }}</div>
          <div>Email : {{ $izin->user->email ?? '-' }}</div>
          <div>Dari : {{ $izin->dari }}</div>
          <div>Sampai : {{ $izin->sampai }}</div>
          <div>Keterangan : {{ $izin->keterangan }}</div>
          <div>Lampiran : @if($izin->lampiran) <a href="/izin/{{ $izin->lampiran }}" target="_blank">{{ $izin->lampiran }}</a> @else - @endif</div>
          <div>Status : @if($izin->status_izin == 1) <span class="badge bg-blue text-blue-fg">Pending</span> @elseif($izin->status_izin == 2) <span class="badge bg-green text-green-fg">Approved</span> @elseif($izin->status_izin == 3) <span class="badge bg-red text-red-fg">Denied</span> @endif</div>
        </div>
        <div class="card-footer">
          <a href="{{ route('admin.sakit') }}" class="btn btn-primary">Back</a>
          @if($izin->status_izin == 1)
          <button type="button" class="btn btn-icon btn-success" data-bs-toggle="modal" data-bs-target="#approve{{ $izin->id }}"><i class="fa-solid fa-check"></i></button>
          <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#reject{{ $izin->id }}"><i class="fa-solid fa-times"></i></button>
          @endif
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
      <form action="{{ route('admin.sakit.approve', $izin->id) }}" method="POST">
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
      <form action="{{ route('admin.sakit.reject', $izin->id) }}" method="POST">
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