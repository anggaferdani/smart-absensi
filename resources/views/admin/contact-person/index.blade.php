@extends('templates.admin')
@section('title', 'Contact Person')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        Contact Person
      </h2>
    </div>
    <div class="col-auto">
      
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
        {{-- <div class="card-header">
          <div class="ms-auto">
            <form action="{{ route('admin.user.index') }}" class="">
              <div class="d-flex gap-1">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="{{ route('admin.user.index') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
              </div>
            </form>
          </div>
        </div> --}}
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>No. HP</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($contactPersons as $contactPerson)
                <tr>
                  <td>{{ ($contactPersons->currentPage() - 1) * $contactPersons->perPage() + $loop->iteration }}</td>
                  <td>{{ $contactPerson->name ?? '-' }}</td>
                  <td>{{ $contactPerson->phone ?? '-' }}</td>
                  <td>
                    <button type="button" class="btn btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#edit{{ $contactPerson->id }}"><i class="fa-solid fa-pen"></i></button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@foreach ($contactPersons as $contactPerson)
<div class="modal modal-blur fade" id="edit{{ $contactPerson->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.contact-person.update', $contactPerson->id) }}" method="POST" class="" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Nama</label>
            <input type="text" class="form-control" name="name" placeholder="Nama" value="{{ $contactPerson->name }}">
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label required">No. HP</label>
            <input type="number" class="form-control" name="phone" placeholder="No. HP" value="{{ $contactPerson->phone }}">
            <div class="small text-muted">Angka 0 pertama diganti dengan 62. Contoh : 62812xxxxxxxx</div>
            @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
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
@endsection