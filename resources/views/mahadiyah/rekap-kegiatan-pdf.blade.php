<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Kepala Kamar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #4318FF;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            color: #111C44;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: bold;
            color: #2B3674;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 11px;
            color: #666;
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #4318FF;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 8px 5px;
            border: 1px solid #3311CC;
            font-size: 9px;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .nama-column {
            text-align: left;
            font-weight: 500;
            min-width: 120px;
        }

        .check-icon {
            color: #05CD99;
            font-weight: bold;
            font-size: 14px;
        }

        .cross-icon {
            color: #EE5D50;
            font-weight: bold;
            font-size: 14px;
        }

        .total-cell {
            font-weight: bold;
            color: #2B3674;
        }

        .percentage-cell {
            font-weight: bold;
            color: #4318FF;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rekap Absensi Kepala Kamar</h1>
        <h2>Pondok Pesantren An-Nur II "Al-Murtadlo"</h2>
        <p><strong>Periode:</strong> {{ $startDate->locale('id')->isoFormat('DD MMMM YYYY') }} -
            {{ $endDate->locale('id')->isoFormat('DD MMMM YYYY') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="min-width: 120px;">Kepala Kamar</th>
                <th colspan="{{ count($dates) }}">Tanggal</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Persentase</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    @php
                        $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $date);
                    @endphp
                    <th>{{ $dateObj->format('d') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $kepkam)
                <tr>
                    <td class="nama-column">{{ $kepkam['nama'] }}</td>
                    @foreach($dates as $date)
                        <td>
                            @if($kepkam['daily_status'][$date])
                                <span class="check-icon">✓</span>
                            @else
                                <span class="cross-icon">✗</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="total-cell">{{ $kepkam['total'] }}</td>
                    <td class="percentage-cell">{{ $kepkam['percentage'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($dates) + 3 }}"
                        style="text-align: center; color: #999; font-style: italic; padding: 20px;">
                        Belum ada data kepala kamar
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}</p>
    </div>
</body>

</html>