<div style="width: 100%; max-width: 900px; margin: 0 auto;">
  <div>
    <div style="padding: 20px;">
      <div style="position: relative; margin-bottom: 30px; text-align: center;">
        <div style="position: absolute; left: 40px; top: 0;">
          <img src="{{ public_path('images/logo2.png') }}" width="55">
        </div>
        <div>
          <div style="font-weight: bold; font-size: 1rem;">PT GUNA CIPTA KREASI</div>
          <div style="font-weight: bold;">Jalan Lapangan Bola No. 7 Kebon Jeruk Jakarta Barat</div>
        </div>
      </div>
      <hr style="margin-bottom: 30px;">
      <div style="text-align: center; font-size: 1rem; font-weight: bold; margin-bottom: 30px;">FORMULIR PENGAJUAN IJIN TIDAK MASUK KARENA SAKIT</div>
      <div style="margin-bottom: 15px;">Kepada YTH</div>
      <div>HRD PT Guna Cipta Kreasi</div>
      <div>di</div>
      <div style="margin-bottom: 15px;">Tempat</div>
      <div style="margin-bottom: 15px;">Perihal: <strong>{{ $izin->keterangan }}</strong></div>
      <div style="margin-bottom: 15px;">Saya yang bertanda tangan dibawah ini:</div>
      <table style="width: 100%; margin-bottom: 15px;">
        <tr>
          <td style="width: 20%;">Nama</td>
          <td>: <strong>{{ $izin->user->name }}</strong></td>
        </tr>
        <tr>
          <td style="width: 20%;">Jabatan</td>
          <td>: <strong>{{ $izin->user->jabatan }}</strong></td>
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
      <div style="margin-bottom: 15px;">
        Bermaksud untuk mengajukan permohonan ijin sakit selama 
        <strong>{{ \Carbon\Carbon::parse($izin->dari)->diffInDays(\Carbon\Carbon::parse($izin->sampai)) + 1 }}</strong> 
        hari dari tanggal 
        <strong>{{ \Carbon\Carbon::parse($izin->dari)->translatedFormat('d') }}</strong> bulan 
        <strong>{{ \Carbon\Carbon::parse($izin->dari)->translatedFormat('F') }}</strong> tahun 
        <strong>{{ \Carbon\Carbon::parse($izin->dari)->translatedFormat('Y') }}</strong> sampai dengan tanggal 
        <strong>{{ \Carbon\Carbon::parse($izin->sampai)->translatedFormat('d') }}</strong> bulan 
        <strong>{{ \Carbon\Carbon::parse($izin->sampai)->translatedFormat('F') }}</strong> tahun 
        <strong>{{ \Carbon\Carbon::parse($izin->sampai)->translatedFormat('Y') }}</strong>.
      </div>
      <div style="margin-bottom: 15px;">
        Demikian surat permohonan ini saya buat dan saya lampirkan surat dokter serta copy resep dokter. Atas perhatian nya saya ucapkan terima kasih.
      </div>
      <div style="margin-bottom: 15px;">
        <strong>{{ \Carbon\Carbon::parse($izin->sampai)->locale('id')->translatedFormat('l, d F Y') }}</strong>
      </div>
      <div style="margin-bottom: 30px;">Hormat Saya,</div>
      <div style="margin-bottom: 30px;"><strong>{{ $izin->user->name }}</strong></div>

      @if($izin->lampiran && $izin->resep_dokter)
      <table style="border-collapse: collapse;">
        <tr>
          <td>Surat Dokter :</td>
          <td>Copy Resep :</td>
        </tr>
        <tr>
          <td valign="top">
            <img src="{{ public_path('sakit/surat-dokter/' . $izin->lampiran) }}" width="130">
          </td>
          <td valign="top">
            <img src="{{ public_path('sakit/resep-dokter/' . $izin->resep_dokter) }}" width="130">
          </td>
        </tr>
      </table>
      @endif
    </div>
  </div>
</div>
