<table style="margin-bottom: 10px;">
    <tbody>
        <tr>
            <td>{{ $month }}</td>
        </tr>
    </tbody>
</table>
<table>
    <tbody>
        <tr>
            <td style="font-size: 10px;">Shift Pagi</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
  <thead>
      <tr>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">No</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Nama</th>
          @for ($day = 1; $day <= $daysInMonth; $day++)
            <th style="border: 1px solid black; text-align: center;" colspan="2">{{ $day }}</th>
          @endfor
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Terlambat</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Overtime</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Jumlah Jam Kerja</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Rata-rata Jam Kerja</th>
      </tr>
      <tr>
          @for ($day = 1; $day <= $daysInMonth; $day++)
            <th style="border: 1px solid black; text-align: center;">M</th>
            <th style="border: 1px solid black; text-align: center;">P</th>
          @endfor
      </tr>
  </thead>
  <tbody>
      @foreach ($absens->where('shift', 'siang')->groupBy('user_id') as $userId => $absenGroup)
          @php
              $izinData = App\Models\Izin::where('user_id', $userId)->get();
              $user = $absenGroup->first()->user;
              $terlambat = $userLateness[$userId][$month] ?? 0;
              $overtime = $userOvertime[$userId][$month] ?? 0;
          
              $totalHours = 0;
              $workHours = [];
              $userIzin = $izinData->where('user_id', $userId);
          @endphp
          <tr>
              <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
              <td style="border: 1px solid black; text-align: center;">{{ $user->name }}</td>
              @for ($day = 1; $day <= $daysInMonth; $day++)
                  @php
                      $izinOnDate = $userIzin->firstWhere(function ($izin) use ($day, $month) {
                          $date = Carbon\Carbon::createFromFormat('F Y', $month)->day($day);
                          return $date->between($izin->dari, $izin->sampai) && $izin->status_process == 1;
                      });

                      $izinStatus = '';
                      if ($izinOnDate) {
                          $izinStatus = $izinOnDate->status_izin == 1 ? 'i' : ($izinOnDate->status_izin == 2 ? 's' : '');
                      }

                      $masuk = $absenGroup->firstWhere(function($absen) use ($day) {
                          return $absen->token->status == 1 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                      });
                      $pulang = $absenGroup->firstWhere(function($absen) use ($day) {
                          return $absen->token->status == 2 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                      });

                      if (!$izinStatus && $masuk && $pulang) {
                          $entryTime = \Carbon\Carbon::parse($masuk->tanggal);
                          $exitTime = \Carbon\Carbon::parse($pulang->tanggal);
                          $hoursWorked = $exitTime->diffInHours($entryTime) + $exitTime->diffInMinutes($entryTime) / 60;
                          $totalHours += $hoursWorked;
                      }
                  @endphp
                  <td style="border: 1px solid black; text-align: center; @if ($masuk && $masuk->status == 3 && $masuk->token->status == 1) color: red; @endif">
                      {{ $izinStatus ? $izinStatus : ($masuk ? \Carbon\Carbon::parse($masuk->tanggal)->format('H:i') : 'a') }}
                  </td>
                  <td style="border: 1px solid black; text-align: center;">
                      {{ $izinStatus ? $izinStatus : ($pulang ? \Carbon\Carbon::parse($pulang->tanggal)->format('H:i') : 'a') }}
                  </td>
              @endfor

              <td style="border: 1px solid black; text-align: center;">{{ $terlambat }}</td>
              <td style="border: 1px solid black; text-align: center;">{{ $overtime }}</td>
              <td style="border: 1px solid black; text-align: center;">{{ number_format($totalHours, 2) }}</td>
              <td style="border: 1px solid black; text-align: center;">
                  @php
                      $averageHours = $daysInMonth ? $totalHours / $daysInMonth : 0;
                  @endphp
                  {{ number_format($averageHours, 2) }}
              </td>
          </tr>
      @endforeach
  </tbody>
</table>
<table>
  <tbody>
      <tr>
        <td style="font-size: 10px;">Shift Malam</td>
      </tr>
  </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
  <thead>
      <tr>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">No</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Nama</th>
          @for ($day = 1; $day <= $daysInMonth; $day++)
            <th style="border: 1px solid black; text-align: center;" colspan="2">{{ $day }}</th>
          @endfor
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Terlambat</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Overtime</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Jumlah Jam Kerja</th>
          <th style="border: 1px solid black; text-align: center;" rowspan="2">Rata-rata Jam Kerja</th>
      </tr>
      <tr>
          @for ($day = 1; $day <= $daysInMonth; $day++)
            <th style="border: 1px solid black; text-align: center;">M</th>
            <th style="border: 1px solid black; text-align: center;">P</th>
          @endfor
      </tr>
  </thead>
  <tbody>
    @foreach ($absens->where('shift', 'malam')->groupBy('user_id') as $userId => $absenGroup)
        @php
            $izinData = App\Models\Izin::where('user_id', $userId)->get();
            $user = $absenGroup->first()->user;
            $terlambat = $userLateness[$userId][$month] ?? 0;
            $overtime = $userOvertime[$userId][$month] ?? 0;
        
            $totalHours = 0;
            $workHours = [];
            $userIzin = $izinData->where('user_id', $userId);
        @endphp
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $user->name }}</td>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $izinOnDate = $userIzin->firstWhere(function ($izin) use ($day, $month) {
                        $date = Carbon\Carbon::createFromFormat('F Y', $month)->day($day);
                        return $date->between($izin->dari, $izin->sampai) && $izin->status_process == 1;
                    });

                    $izinStatus = '';
                    if ($izinOnDate) {
                        $izinStatus = $izinOnDate->status_izin == 1 ? 'i' : ($izinOnDate->status_izin == 2 ? 's' : '');
                    }

                    $masuk = $absenGroup->firstWhere(function($absen) use ($day) {
                        return $absen->token->status == 1 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                    });
                    $pulang = $absenGroup->firstWhere(function($absen) use ($day) {
                        return $absen->token->status == 2 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                    });

                    if (!$izinStatus && $masuk && $pulang) {
                        $entryTime = \Carbon\Carbon::parse($masuk->tanggal);
                        $exitTime = \Carbon\Carbon::parse($pulang->tanggal);
                        $hoursWorked = $exitTime->diffInHours($entryTime) + $exitTime->diffInMinutes($entryTime) / 60;
                        $totalHours += $hoursWorked;
                    }
                @endphp
                <td style="border: 1px solid black; text-align: center; @if ($masuk && $masuk->status == 3 && $masuk->token->status == 1) color: red; @endif">
                    {{ $izinStatus ? $izinStatus : ($masuk ? \Carbon\Carbon::parse($masuk->tanggal)->format('H:i') : 'a') }}
                </td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $izinStatus ? $izinStatus : ($pulang ? \Carbon\Carbon::parse($pulang->tanggal)->format('H:i') : 'a') }}
                </td>
            @endfor

            <td style="border: 1px solid black; text-align: center;">{{ $terlambat }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $overtime }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ number_format($totalHours, 2) }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @php
                    $averageHours = $daysInMonth ? $totalHours / $daysInMonth : 0;
                @endphp
                {{ number_format($averageHours, 2) }}
            </td>
        </tr>
    @endforeach
  </tbody>
</table>