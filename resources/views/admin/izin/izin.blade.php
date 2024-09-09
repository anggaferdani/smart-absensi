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
        <a href="{{ route('admin.izin', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-success">Excel</a>
        <a href="{{ route('admin.izin', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-danger">PDF</a>
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
            <form action="{{ route('admin.izin') }}" class="">
              <div class="d-flex gap-1">
                  <select class="form-select" name="status">
                      <option disabled selected value="">Status</option>
                      <option value="">Semua</option>
                      <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Pending</option>
                      <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Approved</option>
                      <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Denied</option>
                  </select>
                  <input type="date" class="form-control" name="tanggal" value="{{ request('tanggal') }}" placeholder="">
                  <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                  <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                  <a href="{{ route('admin.izin') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
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
                  <td>{{ $izin->dari }}</td>
                  <td>{{ $izin->sampai }}</td>
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
                    <a href="{{ route('admin.izin.show', $izin->kode) }}" class="btn btn-icon btn-primary"><i class="fa-solid fa-eye"></i></a>
                    @if($izin->status_process == 1)
                    <button type="button" class="btn btn-icon btn-success" data-bs-toggle="modal" data-bs-target="#approve{{ $izin->id }}"><i class="fa-solid fa-check"></i></button>
                    <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#reject{{ $izin->id }}"><i class="fa-solid fa-times"></i></button>
                    @endif
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
@endsection