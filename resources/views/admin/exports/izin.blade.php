<table style="width: 100%; border-collapse: collapse;">
  <thead>
      <tr>
          <th style="border: 1px black solid; text-align: center;">Kode</th>
          <th style="border: 1px black solid; text-align: center;">Tanggal</th>
          <th style="border: 1px black solid; text-align: center;">Nama</th>
          <th style="border: 1px black solid; text-align: center;">Keterangan</th>
          <th style="border: 1px black solid; text-align: center;">Jangka Waktu</th>
          <th style="border: 1px black solid; text-align: center;">Status</th>
      </tr>
  </thead>
  <tbody>
      @foreach($izins as $izin)
      <tr>
          <td style="border: 1px black solid; text-align: center;">&nbsp;{{ $izin->kode }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $izin->tanggal }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $izin->user->name }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $izin->keterangan }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $izin->jangka_waktu }} Hari</td>
          <td style="border: 1px black solid; text-align: center;">@if($izin->status == 1) Pending @elseif($izin->status == 2) Approved @elseif($izin->status == 3) Denied @endif</td>
      </tr>
      @endforeach
  </tbody>
</table>