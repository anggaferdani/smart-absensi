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
    </div>
    <div class="col-auto ms-auto">
      <div class="btn-list">
        <a href="{{ route('user.dashboard') }}" class="btn btn-success rounded-pill px-3">Home</a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="fw-bold mb-2">Pengajuan anda sedang dalam proses persetujuan. Cek secara berkala untuk mendapatkan status persetujuan.</div>
    <div>Nama : {{ $izin->user->name }}</div>
    <div>Posisi : {{ $izin->user->jabatan ?? '-' }}</div>
    <div>Dari : {{ \Carbon\Carbon::parse($izin->dari)->format('d M Y') }}</div>
    <div>Sampai dengan : {{ \Carbon\Carbon::parse($izin->sampai)->format('d M Y') }}</div>
    <div class="mb-3">Keterangan : {{ $izin->keterangan }}</div>
    <div class="fw-bold">Lampiran :</div>
    <div class="">1. Surat Dokter : <a href="/sakit/surat-dokter/{{ $izin->lampiran }}" target="_blank">{{ $izin->lampiran }}</a></div>
    <div class="mb-3">2. Copy Resep Dokter : <a href="/sakit/resep-dokter/{{ $izin->resep_dokter }}" target="_blank">{{ $izin->resep_dokter }}</a></div>
    <div class="fw-bold mb-3">SEMOGA LEKAS SEMBUH!</div>
    <div class="text-center fw-bold mb-2">STATUS</div>
    @if($izin->status_process == 1)
      <div class="border border-3 border-dark bg-yellow px-3 py-2 rounded-pill">
        <div class="text-center">MENUNGGU PROSES PERSETUJUAN</div>
      </div>
    @elseif($izin->status_process == 2)
      <div class="border border-3 border-dark bg-green px-3 py-2 rounded-pill">
        <div class="text-center text-white">DISETUJUI</div>
      </div>
    @elseif($izin->status_process == 3)
      <div class="border border-3 border-dark bg-red px-3 py-2 rounded-pill">
        <div class="text-center text-white">TIDAK DISETUJUI</div>
      </div>
    @endif
    @if($izin->status_process == 1)
    <button type="button" class="btn btn-success mt-3 rounded-pill" onclick="sendWhatsApp('{{ route('admin.sakit.show', $izin->kode) }}')">
      <i class="fa-brands fa-whatsapp"></i>&nbsp;Kirim ke WhatsApp
    </button>
    @endif
  </div>
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