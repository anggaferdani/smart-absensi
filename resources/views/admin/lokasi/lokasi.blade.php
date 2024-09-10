@extends('templates.admin')
@section('title', 'Lokasi')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        Lokasi
      </h2>
    </div>
    <div class="col-auto ms-auto d-print-none">
      <div class="btn-list">
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create new report</a>
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
            <form action="{{ route('admin.lokasi.index') }}" class="">
              <div class="d-flex gap-1">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="{{ route('admin.lokasi.index') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
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
                <th>Lat</th>
                <th>Long</th>
                <th>Radius</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($lokasis as $lokasi)
                <tr>
                  <td>{{ ($lokasis->currentPage() - 1) * $lokasis->perPage() + $loop->iteration }}</td>
                  <td>{{ $lokasi->nama }}</td>
                  <td>{{ $lokasi->lat }}</td>
                  <td>{{ $lokasi->long }}</td>
                  <td>{{ $lokasi->radius }}m</td>
                  <td>
                    <a href="{{ route('admin.lokasi.preview', $lokasi->slug) }}" class="btn btn-icon btn-primary"><i class="fa-solid fa-eye"></i></a>
                    <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $lokasi->id }}"><i class="fa-solid fa-pen"></i></button>
                    <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $lokasi->id }}"><i class="fa-solid fa-trash"></i></button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex align-items-center">
          <ul class="pagination m-0 ms-auto">
            @if($lokasis->hasPages())
              {{ $lokasis->appends(request()->query())->links('pagination::bootstrap-4') }}
            @else
              <li class="page-item">No more records</li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.lokasi.store') }}" method="POST" class="">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Create</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Nama</label>
            <input type="text" class="form-control" name="nama" placeholder="Nama">
            @error('nama')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Lokasi</label>
            <textarea class="form-control" name="deskripsi" rows="3" placeholder="Lokasi"></textarea>
            @error('deskripsi')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Lat</label>
            <input type="text" class="form-control" name="lat" placeholder="Lat">
            @error('lat')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Long</label>
            <input type="text" class="form-control" name="long" placeholder="Long">
            @error('long')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Radius</label>
            <input type="number" class="form-control" name="radius" placeholder="Radius">
            @error('radius')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Masuk Shift Pagi</label>
            <input type="time" class="form-control" name="jam_masuk_siang" placeholder="Jam Masuk Shift Pagi">
            @error('jam_masuk_siang')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Pulang Shift Pagi</label>
            <input type="time" class="form-control" name="jam_pulang_siang" placeholder="Jam Pulang Shift Pagi">
            @error('jam_pulang_siang')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Masuk Shift Malam</label>
            <input type="time" class="form-control" name="jam_masuk_malam" placeholder="Jam Masuk Shift malam">
            @error('jam_masuk_malam')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Pulang Shift Malam</label>
            <input type="time" class="form-control" name="jam_pulang_malam" placeholder="Jam Pulang Shift malam">
            @error('jam_pulang_malam')<div class="text-danger">{{ $message }}</div>@enderror
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

@foreach ($lokasis as $lokasi)
<div class="modal modal-blur fade" id="edit{{ $lokasi->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.lokasi.update', $lokasi->id) }}" method="POST" class="">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Nama</label>
            <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ $lokasi->nama }}">
            @error('nama')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Lokasi</label>
            <textarea class="form-control" name="deskripsi" rows="3" placeholder="Lokasi">{{ $lokasi->deskripsi }}</textarea>
            @error('deskripsi')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Lat</label>
            <input type="text" class="form-control" name="lat" placeholder="Lat" value="{{ $lokasi->lat }}">
            @error('lat')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Long</label>
            <input type="text" class="form-control" name="long" placeholder="Long" value="{{ $lokasi->long }}">
            @error('long')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Radius</label>
            <input type="number" class="form-control" name="radius" placeholder="Radius" value="{{ $lokasi->radius }}">
            @error('radius')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Masuk Shift Pagi</label>
            <input type="time" class="form-control" name="jam_masuk_siang" placeholder="Jam Masuk Shift Pagi" value="{{ $lokasi->jam_masuk_siang }}">
            @error('jam_masuk_siang')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Pulang Shift Pagi</label>
            <input type="time" class="form-control" name="jam_pulang_siang" placeholder="Jam Pulang Shift Pagi" value="{{ $lokasi->jam_pulang_siang }}">
            @error('jam_pulang_siang')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Masuk Shift Malam</label>
            <input type="time" class="form-control" name="jam_masuk_malam" placeholder="Jam Masuk Shift Malam" value="{{ $lokasi->jam_masuk_malam }}">
            @error('jam_masuk_malam')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Jam Pulang Shift Malam</label>
            <input type="time" class="form-control" name="jam_pulang_malam" placeholder="Jam Pulang Shift Malam" value="{{ $lokasi->jam_pulang_malam }}">
            @error('jam_pulang_malam')<div class="text-danger">{{ $message }}</div>@enderror
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

@foreach ($lokasis as $lokasi)
<div class="modal modal-blur fade" id="delete{{ $lokasi->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <form action="{{ route('admin.lokasi.destroy', $lokasi->id) }}" method="POST">
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