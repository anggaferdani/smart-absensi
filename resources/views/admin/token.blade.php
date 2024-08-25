@extends('templates.admin')
@section('title', 'Token')
@section('header')
<div class="container-xl">
  <div class="row g-2 align-items-center">
    <div class="col">
      <h2 class="page-title">
        Token
      </h2>
    </div>
    <div class="col-auto ms-auto d-print-none">
      
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
            <form action="{{ route('admin.token') }}" class="">
              <div class="d-flex gap-1">
                <input type="date" class="form-control" name="tanggal" value="{{ request('tanggal') }}" placeholder="">
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search">
                <button type="submit" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="{{ route('admin.token') }}" class="btn btn-icon btn-dark-outline"><i class="fa-solid fa-times"></i></a>
              </div>
            </form>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-striped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Lokasi</th>
                <th>Token</th>
                <th>Tanggal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($tokens as $token)
                <tr>
                  <td>{{ ($tokens->currentPage() - 1) * $tokens->perPage() + $loop->iteration }}</td>
                  <td>{{ $token->lokasi->nama }}</td>
                  <td>{{ $token->token }}</td>
                  <td>{{ $token->tanggal }}</td>
                  <td>
                    @if($token->status == 1)
                    <span class="badge bg-blue text-blue-fg">Masuk</span>
                    @else
                    <span class="badge bg-red text-red-fg">Pulang</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer d-flex align-items-center">
          <ul class="pagination m-0 ms-auto">
            @if($tokens->hasPages())
              {{ $tokens->appends(request()->query())->links('pagination::bootstrap-4') }}
            @else
              <li class="page-item">No more records</li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection