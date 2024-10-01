@foreach ($months as $month => $absens)
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
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">No</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Nama</th>
            @for ($day = 1; $day <= $daysInMonth; $day++)
              <th style="border: 1px solid black; text-align: center; font-size: 5px;" colspan="2">{{ $day }}</th>
            @endfor
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Terlambat</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Overtime</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Jumlah Jam Kerja</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Rata-rata Jam Kerja</th>
        </tr>
        <tr>
            @for ($day = 1; $day <= $daysInMonth; $day++)
              <th style="border: 1px solid black; text-align: center; font-size: 5px;">M</th>
              <th style="border: 1px solid black; text-align: center; font-size: 5px;">P</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($absens->where('shift', 'siang')->groupBy('user_id') as $userId => $absenGroup)
            @php
                $user = $absenGroup->first()->user;
                $terlambat = $userLateness[$userId] ?? 0;
                $overtime = $userOvertime[$userId] ?? 0;
        
                $totalHours = 0;
                $workHours = [];
            @endphp
            <tr>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $user->name }}</td>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $masuk = $absenGroup->firstWhere(function($absen) use ($day) {
                        return $absen->token->status == 1 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                    });
                    $pulang = $absenGroup->firstWhere(function($absen) use ($day) {
                        return $absen->token->status == 2 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                    });
        
                    if ($masuk && $pulang) {
                        $entryTime = \Carbon\Carbon::parse($masuk->tanggal);
                        $exitTime = \Carbon\Carbon::parse($pulang->tanggal);
                        $hoursWorked = $exitTime->diffInHours($entryTime) + $exitTime->diffInMinutes($entryTime) / 60;
                        $totalHours += $hoursWorked;
                    }
                @endphp
                    <td style="border: 1px solid black; text-align: center; font-size: 5px; 
                        @if ($masuk && $masuk->status == 3 && $masuk->token->status == 1)
                            color: red;
                        @endif
                    ">
                        {{ $masuk ? \Carbon\Carbon::parse($masuk->tanggal)->format('H:i') : '' }}
                    </td>
                    <td style="border: 1px solid black; text-align: center; font-size: 5px;">
                        {{ $pulang ? \Carbon\Carbon::parse($pulang->tanggal)->format('H:i') : '' }}
                    </td>
                @endfor
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $terlambat }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $overtime }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ number_format($totalHours, 2) }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">
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
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">No</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Nama</th>
            @for ($day = 1; $day <= $daysInMonth; $day++)
              <th style="border: 1px solid black; text-align: center; font-size: 5px;" colspan="2">{{ $day }}</th>
            @endfor
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Terlambat</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Overtime</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Jumlah Jam Kerja</th>
            <th style="border: 1px solid black; text-align: center; font-size: 5px;" rowspan="2">Rata-rata Jam Kerja</th>
        </tr>
        <tr>
            @for ($day = 1; $day <= $daysInMonth; $day++)
              <th style="border: 1px solid black; text-align: center; font-size: 5px;">M</th>
              <th style="border: 1px solid black; text-align: center; font-size: 5px;">P</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($absens->where('shift', 'malam')->groupBy('user_id') as $userId => $absenGroup)
            @php
                $user = $absenGroup->first()->user;
                $currentMonthYear = Carbon::parse($month)->format('Y-m');
                $terlambat = $userLateness[$userId][$currentMonthYear] ?? 0;
                $overtime = $userOvertime[$userId][$currentMonthYear] ?? 0;
        
                $totalHours = 0;
                $workHours = [];
            @endphp
            <tr>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $user->name }}</td>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $masuk = $absenGroup->firstWhere(function($absen) use ($day) {
                        return $absen->token->status == 1 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                    });
                    $pulang = $absenGroup->firstWhere(function($absen) use ($day) {
                        return $absen->token->status == 2 && \Carbon\Carbon::parse($absen->tanggal)->day == $day;
                    });
        
                    if ($masuk && $pulang) {
                        $entryTime = \Carbon\Carbon::parse($masuk->tanggal);
                        $exitTime = \Carbon\Carbon::parse($pulang->tanggal);
                        $hoursWorked = $exitTime->diffInHours($entryTime) + $exitTime->diffInMinutes($entryTime) / 60;
                        $totalHours += $hoursWorked;
                    }
                @endphp
                    <td style="border: 1px solid black; text-align: center; font-size: 5px; 
                        @if ($masuk && $masuk->status == 3 && $masuk->token->status == 1)
                            color: red;
                        @endif
                    ">
                        {{ $masuk ? \Carbon\Carbon::parse($masuk->tanggal)->format('H:i') : '' }}
                    </td>
                    <td style="border: 1px solid black; text-align: center; font-size: 5px;">
                        {{ $pulang ? \Carbon\Carbon::parse($pulang->tanggal)->format('H:i') : '' }}
                    </td>
                @endfor
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $terlambat }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ $overtime }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">{{ number_format($totalHours, 2) }}</td>
                <td style="border: 1px solid black; text-align: center; font-size: 5px;">
                    @php
                        $averageHours = $daysInMonth ? $totalHours / $daysInMonth : 0;
                    @endphp
                    {{ number_format($averageHours, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
@endforeach
