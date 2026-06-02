<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Pengurus</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #4318FF;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            color: #111C44;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 12px;
            font-weight: bold;
            color: #2B3674;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th {
            background-color: #4318FF;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #3311CC;
            font-size: 8px;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
        }

        .divider-row {
            background-color: #F4F7FE;
            font-weight: bold;
            text-align: left;
            padding-left: 8px;
            font-size: 8px;
            color: #555;
            border: 1px solid #ddd;
        }

        .nama-column {
            text-align: left;
            font-weight: bold;
            min-width: 130px;
        }

        .jabatan-sub {
            display: block;
            font-size: 7px;
            color: #888;
            font-weight: normal;
            margin-top: 1px;
        }

        .badge-container {
            white-space: nowrap;
        }

        .badge {
            display: inline-block;
            width: 11px;
            height: 11px;
            line-height: 11px;
            border-radius: 2px;
            text-align: center;
            font-size: 7px;
            font-weight: bold;
            margin: 0 1px;
        }

        .status-H { background-color: #DEF7EC; color: #03543F; }
        .status-S { background-color: #FEF3C7; color: #92400E; }
        .status-I { background-color: #E1EFFE; color: #1E429F; }
        .status-A { background-color: #FDE8E8; color: #9B1C1C; }
        .status-none { color: #ccc; background-color: #f3f3f3; }

        .summary-cell {
            font-weight: bold;
            color: #111C44;
            background-color: #F4F7FE;
        }

        .summary-H {
            color: #03543F;
        }

        .summary-total {
            color: #666;
            font-weight: normal;
            font-size: 7.5px;
        }

        .legend {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #eee;
            font-size: 8px;
            color: #555;
        }

        .legend-item {
            display: inline-block;
            margin-right: 15px;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rekap Absensi Kehadiran Pengurus</h1>
        <h2>Pondok Pesantren An-Nur II "Al-Murtadlo"</h2>
        <p><strong>Periode:</strong> {{ $startDate->locale('id')->isoFormat('DD MMMM YYYY') }} -
            {{ $endDate->locale('id')->isoFormat('DD MMMM YYYY') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="min-width: 130px;">Pengurus</th>
                <th colspan="{{ count($dates) }}">Tanggal</th>
                <th colspan="3">Total Hadir</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    @php
                        $dateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $date);
                    @endphp
                    <th>{{ $dateObj->format('d') }}</th>
                @endforeach
                <th style="background-color: #3311CC; font-size: 7.5px; width: 45px;">Bandongan</th>
                <th style="background-color: #3311CC; font-size: 7.5px; width: 45px;">Wirid</th>
                <th style="background-color: #3311CC; font-size: 7.5px; width: 45px;">Yasinan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentDivisi = null;
            @endphp
            @forelse($rekapData as $row)
                @if($row['divisi'] !== $currentDivisi)
                    @php
                        $currentDivisi = $row['divisi'];
                    @endphp
                    <tr>
                        <td colspan="{{ count($dates) + 4 }}" class="divider-row">
                            Divisi: {{ $currentDivisi }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="nama-column">
                        {{ $row['nama'] }}
                        <span class="jabatan-sub">{{ $row['jabatan'] }}</span>
                    </td>
                    @foreach($dates as $date)
                        @php
                            $att = $row['attendance'][$date];
                        @endphp
                        <td>
                            <div class="badge-container">
                                <!-- Bandongan Badge -->
                                @if($att['bandongan'])
                                    <span class="badge status-{{ $att['bandongan'] }}">B</span>
                                @else
                                    <span class="badge status-none">-</span>
                                @endif

                                <!-- Wirid Badge -->
                                @if($att['wirid'])
                                    <span class="badge status-{{ $att['wirid'] }}">W</span>
                                @else
                                    <span class="badge status-none">-</span>
                                @endif

                                <!-- Yasinan Badge -->
                                @if($row['tipe'] !== 'kepkam')
                                    @if($att['yasinan'])
                                        <span class="badge status-{{ $att['yasinan'] }}">Y</span>
                                    @else
                                        <span class="badge status-none">-</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    @endforeach
                    <!-- Bandongan Summary -->
                    <td class="summary-cell">
                        <span class="summary-H">{{ $row['summary']['bandongan']['H'] }}</span><span class="summary-total">/{{ $row['summary']['bandongan']['total'] }}</span>
                    </td>
                    <!-- Wirid Summary -->
                    <td class="summary-cell">
                        <span class="summary-H">{{ $row['summary']['wirid']['H'] }}</span><span class="summary-total">/{{ $row['summary']['wirid']['total'] }}</span>
                    </td>
                    <!-- Yasinan Summary -->
                    <td class="summary-cell">
                        @if($row['tipe'] === 'kepkam')
                            <span style="color: #ccc;">-</span>
                        @else
                            <span class="summary-H">{{ $row['summary']['yasinan']['H'] }}</span><span class="summary-total">/{{ $row['summary']['yasinan']['total'] }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 4 }}"
                        style="text-align: center; color: #999; font-style: italic; padding: 15px;">
                        Belum ada data absensi pengurus
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="legend">
        <div class="legend-item">
            <strong>Legenda Status:</strong>
        </div>
        <div class="legend-item">
            <span class="badge status-H">H</span> Hadir
        </div>
        <div class="legend-item">
            <span class="badge status-S">S</span> Sakit
        </div>
        <div class="legend-item">
            <span class="badge status-I">I</span> Izin
        </div>
        <div class="legend-item">
            <span class="badge status-A">A</span> Alpa
        </div>
        <div class="legend-item" style="margin-left: 20px;">
            <strong>Keterangan:</strong> 
            <span class="badge status-none">B</span> Bandongan
            <span class="badge status-none">W</span> Wirid
            <span class="badge status-none">Y</span> Yasinan (Khusus Non Kepala Kamar)
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}</p>
    </div>
</body>

</html>
