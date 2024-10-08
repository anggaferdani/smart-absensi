@extends('templates.user')
@section('title', 'Izin')
@section('header')
<div class="row">
  <div class="d-flex align-items-center">
    <div class="d-flex justify-content-center p-3"><img src="/profile-picture/{{ auth()->user()->profile_picture }}" alt="" class="rounded-circle border border-dark border-3" width="70"></div>
    <div>
      <div class="text-white fs-1">{{ auth()->user()->name }}</div>
      <div class="text-white fs-3">{{ auth()->user()->jabatan }}</div>
    </div>
  </div>
</div>
@endsection
@section('content')
<div style="border-radius: 70px; border-bottom-left-radius: 0; border-bottom-right-radius: 0;" class="bg-white p-0 px-5 py-5 vh-100">
  <div class="row g-2 align-items-center mb-3">
    <div class="col">
      <h2 class="page-title">Izin</h2>
    </div>
    <div class="col-auto ms-auto">
      <div class="btn-list">
        <a href="{{ route('user.izin.create') }}" class="btn btn-primary rounded-pill px-3">Ajukan Izin</a>
        <a href="{{ route('user.dashboard') }}" class="btn btn-success rounded-pill px-3">Home</a>
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
          <div class="card-body">
            <div>Kode : {{ $izin->kode }}</div>
            <div>Dibuat : {{ \Carbon\Carbon::parse($izin->created_at)->format('d M Y H:m:s') }}</div>
            <div>Keterangan : {{ $izin->keterangan }}</div>
            <div>Status : @if($izin->status_process == 1) <span class="badge bg-blue text-blue-fg">Pending</span> @elseif($izin->status_process == 2) <span class="badge bg-green text-green-fg">Approved</span> @elseif($izin->status_process == 3) <span class="badge bg-red text-red-fg">Denied</span> @endif</div>
          </div>
          <div class="card-footer">
            <a href="{{ route('user.izin.show', $izin->kode) }}" class="btn btn-icon btn-primary"><i class="fa-solid fa-eye"></i></a>
            @if($izin->status_process == 1)
              <a href="{{ route('user.izin.edit', $izin->id) }}" class="btn btn-icon btn-primary"><i class="fa-solid fa-pen"></i></a>
              <button type="button" class="btn btn-icon btn-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $izin->id }}"><i class="fa-solid fa-trash"></i></button>
              <button type="button" class="btn btn-success" onclick="sendWhatsApp('{{ route('admin.izin.show', $izin->kode) }}')">
                <i class="fa-brands fa-whatsapp"></i>&nbsp;Kirim ke WhatsApp
              </button>
            @endif
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

  @include('templates.footer')

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
</div>
@endsection
@push('scripts')
<script>
  function sendWhatsApp(kode) {
      const phoneNumber = '{{ $contactPerson->phone }}';
      const message = `Halo\n\nSaya ingin memberitahukan bahwa saya telah mengajukan permohonan izin. berikut\n\n${kode}\n\nLink berikut yang menunjukkan tentang pengajuan saya, termasuk tanggal dan alasan permohonan.\n\nTerima kasih.`;
      const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
      window.open(whatsappUrl, '_blank');
  }
</script>
@endpush