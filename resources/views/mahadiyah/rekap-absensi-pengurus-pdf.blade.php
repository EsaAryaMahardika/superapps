<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Pengurus</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            line-height: 1.3;
            color: #333;
            padding: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 3px solid #4318FF;
            padding-bottom: 8px;
        }
        .header h1 { font-size: 13px; font-weight: bold; color: #111C44; margin-bottom: 2px; }
        .header h2 { font-size: 11px; font-weight: bold; color: #2B3674; margin-bottom: 4px; }
        .header p  { font-size: 9px; color: #666; margin: 2px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 4px; }

        /* Header utama */
        th {
            background-color: #4318FF;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 5px 3px;
            border: 1px solid #3311CC;
            font-size: 7.5px;
        }
        th.summary-th { background-color: #3311CC; width: 38px; font-size: 7px; }

        /* Baris total hadir (thead baris ke-3) */
        .total-hadir-th {
            background-color: #1B2559;
            color: white;
            font-size: 7px;
            padding: 4px 2px;
            border: 1px solid #2d3a6b;
            text-align: center;
            font-weight: bold;
        }
        .total-hadir-th.label { text-align: left; padding-left: 6px; }
        .total-hadir-th .val { color: #86efac; font-weight: bold; }
        .total-hadir-th.summary-total { background-color: #151d47; }

        td {
            padding: 4px 3px;
            border: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7.5px;
        }

        /* Baris divisi */
        .divider-row td {
            background-color: #F8F9FD;
            font-weight: bold;
            text-align: left;
            padding-left: 8px;
            font-size: 7.5px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Kolom nama */
        .nama-column {
            text-align: left;
            font-weight: bold;
            color: #1B2559;
            min-width: 100px;
            max-width: 130px;
        }
        .jabatan-sub {
            display: block;
            font-size: 6.5px;
            color: #9CA3AF;
            font-weight: normal;
            margin-top: 1px;
        }

        /* Badge B/W/Y */
        .badge-container { white-space: nowrap; }
        .badge {
            display: inline-block;
            width: 10px;
            height: 10px;
            line-height: 10px;
            border-radius: 2px;
            text-align: center;
            font-size: 6.5px;
            font-weight: bold;
            margin: 0 0.5px;
        }
        .status-H    { background-color: #DEF7EC; color: #03543F; }
        .status-S    { background-color: #FEF3C7; color: #92400E; }
        .status-I    { background-color: #DBEAFE; color: #1E40AF; }
        .status-A    { background-color: #FEE2E2; color: #991B1B; }
        .status-L    { background-color: #F3E8FF; color: #6B21A8; }
        .status-none { color: #D1D5DB; background-color: #F9FAFB; border: 1px dashed #D1D5DB; }

        /* Summary kolom */
        .summary-cell {
            font-weight: bold;
            background-color: #F4F7FE;
            color: #1B2559;
        }
        .summary-H     { color: #059669; font-weight: bold; }
        .summary-slash { color: #D1D5DB; font-size: 6.5px; }
        .summary-tot   { color: #6B7280; font-weight: normal; font-size: 6.5px; }

        /* Legend */
        .legend {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #eee;
            font-size: 7.5px;
            color: #555;
        }
        .legend-item { display: inline-block; margin-right: 12px; }

        .footer {
            margin-top: 10px;
            text-align: right;
            font-size: 7px;
            color: #9CA3AF;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rekap Absensi Kehadiran Pengurus</h1>
        <h2>Pondok Pesantren An-Nur II "Al-Murtadlo"</h2>
        <p>
            <strong>Periode:</strong>
            {{ $startDate->locale('id')->isoFormat('DD MMMM YYYY') }} &ndash;
            {{ $endDate->locale('id')->isoFormat('DD MMMM YYYY') }}
            @if(isset($tipe) && $tipe !== 'all')
                &nbsp;&bull;&nbsp; <strong>Filter:</strong> {{ ucfirst($tipe) }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            {{-- Baris 1: Header kolom utama --}}
            <tr>
                <th rowspan="2" style="text-align:left; padding-left:6px; min-width:100px;">Pengurus</th>
                @foreach($dates as $date)
                    @php $dateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $date); @endphp
                    <th>
                        <span style="display:block; font-size:6px; color:#BFD0FF; font-weight:normal; text-transform:uppercase;">
                            {{ $dateObj->locale('id')->isoFormat('dd') }}
                        </span>
                        <span style="display:block; font-size:9px; font-weight:900; margin-top:1px;">
                            {{ $dateObj->format('d') }}
                        </span>
                    </th>
                @endforeach
                <th class="summary-th" rowspan="2">Bandongan</th>
                <th class="summary-th" rowspan="2">Wirid</th>
                <th class="summary-th" rowspan="2">Yasinan</th>
            </tr>

            {{-- Baris 2: Total hadir per tanggal --}}
            @if(isset($dailySummary) && count($rekapData) > 0)
            @php $tipeKey = isset($tipe) ? $tipe : 'all'; @endphp
            <tr>
                @foreach($dates as $date)
                    @php $s = $dailySummary[$tipeKey][$date] ?? ['bandongan'=>0,'bandongan_total'=>0,'wirid'=>0,'wirid_total'=>0,'yasinan'=>0,'yasinan_total'=>0]; @endphp
                    <td class="total-hadir-th">
                        <span style="display:block;"><span style="color:#6EE7B7;font-size:5.5px;">B</span> <span class="val">{{ $s['bandongan'] }}/{{ $s['bandongan_total'] }}</span></span>
                        <span style="display:block;"><span style="color:#6EE7B7;font-size:5.5px;">W</span> <span class="val">{{ $s['wirid'] }}/{{ $s['wirid_total'] }}</span></span>
                        @if($tipeKey !== 'kepkam')
                        <span style="display:block;"><span style="color:#6EE7B7;font-size:5.5px;">Y</span> <span class="val">{{ $s['yasinan'] }}/{{ $s['yasinan_total'] }}</span></span>
                        @endif
                    </td>
                @endforeach
            </tr>
            @endif
        </thead>
        <tbody>
            @php $currentDivisi = null; @endphp
            @forelse($rekapData as $row)
                {{-- Baris divisi separator --}}
                @if($row['divisi'] !== $currentDivisi)
                    @php $currentDivisi = $row['divisi']; @endphp
                    <tr class="divider-row">
                        <td colspan="{{ count($dates) + 4 }}">&#128193; Divisi: {{ $currentDivisi }}</td>
                    </tr>
                @endif

                <tr>
                    <td class="nama-column">
                        {{ $row['nama'] }}
                        <span class="jabatan-sub">{{ $row['jabatan'] }}</span>
                    </td>

                    @foreach($dates as $date)
                        @php $att = $row['attendance'][$date]; @endphp
                        <td>
                            <div class="badge-container">
                                {{-- Bandongan --}}
                                @if($att['bandongan'])
                                    <span class="badge status-{{ $att['bandongan'] }}">B</span>
                                @else
                                    <span class="badge status-none">-</span>
                                @endif

                                {{-- Wirid --}}
                                @if($att['wirid'])
                                    <span class="badge status-{{ $att['wirid'] }}">W</span>
                                @else
                                    <span class="badge status-none">-</span>
                                @endif

                                {{-- Yasinan (non kepkam only) --}}
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

                    {{-- Summary: Bandongan --}}
                    <td class="summary-cell">
                        <span class="summary-H">{{ $row['summary']['bandongan']['H'] }}</span><span class="summary-slash">/</span><span class="summary-tot">{{ $row['summary']['bandongan']['total'] }}</span>
                    </td>
                    {{-- Summary: Wirid --}}
                    <td class="summary-cell">
                        <span class="summary-H">{{ $row['summary']['wirid']['H'] }}</span><span class="summary-slash">/</span><span class="summary-tot">{{ $row['summary']['wirid']['total'] }}</span>
                    </td>
                    {{-- Summary: Yasinan --}}
                    <td class="summary-cell">
                        @if($row['tipe'] === 'kepkam')
                            <span style="color:#D1D5DB;">-</span>
                        @else
                            <span class="summary-H">{{ $row['summary']['yasinan']['H'] }}</span><span class="summary-slash">/</span><span class="summary-tot">{{ $row['summary']['yasinan']['total'] }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 4 }}" style="text-align:center; color:#9CA3AF; font-style:italic; padding:15px;">
                        Belum ada data absensi pengurus
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="legend">
        <span class="legend-item"><strong>Status:</strong></span>
        <span class="legend-item"><span class="badge status-H">H</span> Hadir</span>
        <span class="legend-item"><span class="badge status-S">S</span> Sakit</span>
        <span class="legend-item"><span class="badge status-I">I</span> Izin</span>
        <span class="legend-item"><span class="badge status-A">A</span> Alpa</span>
        <span class="legend-item"><span class="badge status-L">L</span> Libur</span>
        <span class="legend-item" style="margin-left:15px;"><strong>Kegiatan:</strong></span>
        <span class="legend-item"><span class="badge" style="background:#E5E7EB;color:#374151;">B</span> Bandongan</span>
        <span class="legend-item"><span class="badge" style="background:#E5E7EB;color:#374151;">W</span> Wirid</span>
        <span class="legend-item"><span class="badge" style="background:#E5E7EB;color:#374151;">Y</span> Yasinan (Non KepKam)</span>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}</p>
    </div>
</body>

</html>
