<table style="width: 100%; border-collapse: collapse;">
  <thead>
      <tr>
          <th style="border: 1px black solid; text-align: center;">Kode</th>
          <th style="border: 1px black solid; text-align: center;">Dari</th>
          <th style="border: 1px black solid; text-align: center;">Sampai</th>
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
          <td style="border: 1px black solid; text-align: center;">{{ \Carbon\Carbon::parse($izin->dari)->format('d-m-Y') }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ \Carbon\Carbon::parse($izin->sampai)->format('d-m-Y') }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $izin->user->name }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $izin->keterangan }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ \Carbon\Carbon::parse($izin->dari)->diffInDays(\Carbon\Carbon::parse($izin->sampai)) + 1 }} Hari</td>
          <td style="border: 1px black solid; text-align: center;">@if($izin->status_process == 1) Pending @elseif($izin->status_process == 2) Approved @elseif($izin->status_process == 3) Denied @endif</td>
      </tr>
      @endforeach
  </tbody>
</table>