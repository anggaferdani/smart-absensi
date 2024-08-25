<table style="width: 100%; border-collapse: collapse;">
  <thead>
      <tr>
          <th style="border: 1px black solid; text-align: center;">Kode</th>
          <th style="border: 1px black solid; text-align: center;">Token</th>
          <th style="border: 1px black solid; text-align: center;">Lokasi</th>
          <th style="border: 1px black solid; text-align: center;">Tanggal</th>
          <th style="border: 1px black solid; text-align: center;">Status</th>
          <th style="border: 1px black solid; text-align: center;">Nama</th>
          <th style="border: 1px black solid; text-align: center;">Status</th>
      </tr>
  </thead>
  <tbody>
      @foreach($absens as $absen)
      <tr>
          <td style="border: 1px black solid; text-align: center;">&nbsp;{{ $absen->kode }}</td>
          <td style="border: 1px black solid; text-align: center;">&nbsp;{{ $absen->token->token }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $absen->token->lokasi->nama }}</td>
          <td style="border: 1px black solid; text-align: center;">{{ $absen->tanggal }}</td>
          <td style="border: 1px black solid; text-align: center;">@if($absen->token->status == 1) Masuk @elseif($absen->token->status == 2) Pulang @endif</td>
          <td style="border: 1px black solid; text-align: center;">{{ $absen->user->name }}</td>
          <td style="border: 1px black solid; text-align: center;">@if($absen->status == 1) Lebih Awal @elseif($absen->status == 2) Tepat Waktu @elseif($absen->status == 3) Terlambat @endif</td>
      </tr>
      @endforeach
  </tbody>
</table>