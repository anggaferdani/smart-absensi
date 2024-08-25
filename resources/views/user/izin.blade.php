@extends('templates.user')
@section('title', 'Izin')
@section('content')
<div class="row g-2 align-items-center mb-3">
  <div class="col">
    <h2 class="page-title">Izin</h2>
  </div>
  <div class="col-auto ms-auto">
    <div class="btn-list">
      <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Ajukan Izin</a>
    </div>
  </div>
</div>
<div class="row">
  <div>
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
</div>
<div class="row row-cards mb-3">
  @forelse($izins as $izin)
    <div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Kode {{ $izin->kode }}</h3>
        </div>
        <div class="card-body">
          <div>Tanggal : {{ $izin->tanggal }}</div>
          <div>Keterangan : {{ $izin->keterangan }}</div>
          <div>Jangka Waktu : {{ $izin->jangka_waktu }} Hari</div>
          <div>Status : @if($izin->status_izin == 1) <span class="badge bg-blue text-blue-fg">Pending</span> @elseif($izin->status_izin == 2) <span class="badge bg-green text-green-fg">Approved</span> @elseif($izin->status_izin == 3) <span class="badge bg-red text-red-fg">Denied</span> @endif</div>
        </div>
        <div class="card-footer">
          <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $izin->id }}"><i class="fa-solid fa-pen"></i></button>
          <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $izin->id }}"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
    </div>
  @empty
    <div><img src="{{ asset('images/bloom-a-man-looks-at-a-blank-sheet-of-paper-in-puzzlement.png') }}" alt="" class="img-fluid m-auto"></div>
  @endforelse
</div>

<div class="d-flex justify-content-center">
  <ul class="pagination m-0">
    @if($izins->hasPages())
      {{ $izins->appends(request()->query())->links('pagination::bootstrap-4') }}
    @else
      <li class="page-item">No more records</li>
    @endif
  </ul>
</div>

<div class="modal modal-blur fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('user.izin.store') }}" method="POST" class="">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Create</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Keterangan</label>
            <textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan"></textarea>
            @error('keterangan')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" placeholder="Tanggal">
            @error('tanggal')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jangka Waktu Berapa Hari?</label>
            <input type="number" class="form-control" name="jangka_waktu" placeholder="Jangka Waktu">
            @error('jangka_waktu')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

@foreach ($izins as $izin)
<div class="modal modal-blur fade" id="edit{{ $izin->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('user.izin.update', $izin->id) }}" method="POST" class="">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Keterangan</label>
            <textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan">{{ $izin->keterangan }}</textarea>
            @error('keterangan')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" placeholder="Tanggal" value="{{ $izin->tanggal }}">
            @error('tanggal')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jangka Waktu Berapa Hari?</label>
            <input type="number" class="form-control" name="jangka_waktu" placeholder="Jangka Waktu" value="{{ $izin->jangka_waktu }}">
            @error('jangka_waktu')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
          <button type="submit" class="btn btn-primary">Submit</button>
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
      <form action="{{ route('user.izin.destroy', $izin->id) }}" method="POST">
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
@endsection