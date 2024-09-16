@extends('templates.admin')
@section('title', 'User')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        User
      </h2>
    </div>
    <div class="col-auto">
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
            <form action="{{ route('admin.user.index') }}" class="">
              <div class="d-flex gap-1">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="{{ route('admin.user.index') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Profile Picture</th>
                <th>Name</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Jabatan</th>
                <th>Unit Kerja</th>
                <th>Lokasi</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($users as $user)
                <tr>
                  <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                  <td><a href="/profile-picture/{{ $user->profile_picture }}" target="_blank"><img src="/profile-picture/{{ $user->profile_picture }}" alt="" class="img-fluid rounded-circle" width="70"></a></td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->phone ?? '-' }}</td>
                  <td>{{ $user->email ?? '-' }}</td>
                  <td>{{ $user->jabatan ?? '-' }}</td>
                  <td>{{ $user->unitKerja->nama ?? '-' }}</td>
                  <td>{{ $user->lokasi->nama ?? '-' }}</td>
                  <td>
                    <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $user->id }}"><i class="fa-solid fa-pen"></i></button>
                    <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $user->id }}"><i class="fa-solid fa-trash"></i></button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex align-items-center">
          <ul class="pagination m-0 ms-auto">
            @if($users->hasPages())
              {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
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
      <form action="{{ route('admin.user.store') }}" method="POST" class="" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Create</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" class="form-control" name="profile_picture" placeholder="Profile Picture">
            <div class="small text-muted">Foto profil harus memiliki rasio 1:1.</div>
            @error('profile_picture')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Lokasi</label>
            <select class="form-select" name="lokasi_id">
              <option disabled selected value="">Pilih</option>
              @foreach($lokasis as $lokasi)
                  <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
              @endforeach
            </select>
            @error('lokasi_id')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Name">
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">No. HP</label>
            <input type="number" class="form-control" name="phone" placeholder="No. HP">
            @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
          <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email">
            @error('email')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
            @error('password')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <input type="text" class="form-control" name="jabatan" placeholder="Jabatan">
            @error('jabatan')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Unit Kerja</label>
            <select class="form-select" name="unit_kerja_id">
              <option disabled selected value="">Pilih</option>
              @foreach($unitKerjas as $unitKerja)
                  <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama }}</option>
              @endforeach
            </select>
            @error('unit_kerja_id')<div class="text-danger">{{ $message }}</div>@enderror
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

@foreach ($users as $user)
<div class="modal modal-blur fade" id="edit{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.user.update', $user->id) }}" method="POST" class="" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" class="form-control" name="profile_picture" placeholder="Profile Picture">
            <div class="small text-muted">Foto profil harus memiliki rasio 1:1.</div>
            <a href="/profile-picture/{{ $user->profile_picture }}" target="_blank">{{ $user->profile_picture }}</a>
            @error('profile_picture')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Lokasi</label>
            <select class="form-select" name="lokasi_id">
              <option disabled selected value="">Pilih</option>
              @foreach($lokasis as $lokasi)
                <option value="{{ $lokasi->id }}" @if($user->lokasi_id == $lokasi->id) @selected(true) @endif>{{ $lokasi->nama }}</option>
              @endforeach
            </select>
            @error('lokasi_id')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $user->name }}">
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">No. HP</label>
            <input type="number" class="form-control" name="phone" placeholder="No. HP" value="{{ $user->phone }}">
            @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email" value="{{ $user->email }}">
            @error('email')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
            @error('password')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <input type="text" class="form-control" name="jabatan" placeholder="Jabatan" value="{{ $user->jabatan }}">
            @error('jabatan')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">Unit Kerja</label>
            <select class="form-select" name="unit_kerja_id">
              <option disabled selected value="">Pilih</option>
              @foreach($unitKerjas as $unitKerja)
                <option value="{{ $unitKerja->id }}" @if($user->unit_kerja_id == $unitKerja->id) @selected(true) @endif>{{ $unitKerja->nama }}</option>
              @endforeach
            </select>
            @error('unit_kerja_id')<div class="text-danger">{{ $message }}</div>@enderror
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

@foreach ($users as $user)
<div class="modal modal-blur fade" id="delete{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST">
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