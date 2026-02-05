<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekap Harian Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 8px;
            background-color: #FAFBFC;
        }

        .container {
            background-color: white;
            padding: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 2px solid #D1D5DB;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 8px 0;
            font-weight: bold;
            color: #1B2559;
        }

        .header h2 {
            font-size: 14px;
            margin: 0 0 10px 0;
            font-weight: bold;
            color: #1B2559;
        }

        .header-info {
            font-size: 9px;
            color: #2B3674;
            line-height: 1.5;
        }

        .header-info p {
            margin: 3px 0;
        }

        .header-info strong {
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead {
            background-color: #F3F4F6;
        }

        th {
            padding: 8px 5px;
            text-align: center;
            font-size: 8px;
            font-weight: 800;
            color: #2B3674;
            text-transform: uppercase;
            border-bottom: 3px solid #D1D5DB;
            border-right: 1px solid #E5E7EB;
        }

        th:last-child {
            border-right: none;
        }

        th.th-no {
            text-align: center;
            width: 30px;
        }

        th.th-nama {
            text-align: left;
            padding-left: 8px;
            min-width: 120px;
        }

        tbody tr {
            border-bottom: 1px solid #F3F4F6;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 6px 4px;
            text-align: center;
            font-size: 8px;
            border-right: 1px solid #E5E7EB;
        }

        td:last-child {
            border-right: none;
        }

        td.no {
            font-weight: 500;
            color: #2B3674;
            text-align: center;
        }

        td.nama {
            text-align: left;
            font-weight: 500;
            color: #2B3674;
            padding-left: 8px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7px;
            font-weight: bold;
        }

        /* Warna badge sesuai web view */
        .badge-h {
            background-color: #D1FAE5;
            color: #059669;
        }

        .badge-s {
            background-color: #FEF3C7;
            color: #D97706;
        }

        .badge-i {
            background-color: #DBEAFE;
            color: #2563EB;
        }

        .badge-a {
            background-color: #FEE2E2;
            color: #DC2626;
        }

        .badge-default {
            background-color: #F3F4F6;
            color: #A3AED0;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #A3AED0;
            padding-top: 10px;
            border-top: 1px solid #E5E7EB;
        }

        .footer p {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Absensi Harian Santri</h1>
            <h2>Pondok Pesantren An-Nur II "Al-Murtadlo"</h2>
            <div class="header-info">
                <p><strong>Kepala Kamar:</strong> {{ $kepalaKamar }}</p>
                <p><strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="th-no">NO</th>
                    <th class="th-nama">NAMA SANTRI</th>
                    @foreach ($activities as $activity)
                        <th>{{ strtoupper($activity['name']) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $statusBadge = [
                        'H' => 'badge-h',
                        'S' => 'badge-s',
                        'I' => 'badge-i',
                        'A' => 'badge-a',
                    ];
                    $statusLabel = [
                        'H' => 'Hadir',
                        'S' => 'Sakit',
                        'I' => 'Izin',
                        'A' => 'Alfa',
                    ];
                @endphp

                @foreach ($rekapData as $row)
                    <tr>
                        <td class="no">{{ $row['no'] }}</td>
                        <td class="nama">{{ $row['nama'] }}</td>
                        @foreach ($activities as $activity)
                            @php
                                $status = $row['attendance'][$activity['name']] ?? '-';
                            @endphp
                            <td>
                                @if ($status !== '-')
                                    <span class="badge {{ $statusBadge[$status] ?? 'badge-default' }}">
                                        {{ $statusLabel[$status] ?? $status }}
                                    </span>
                                @else
                                    <span class="badge badge-default">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh sistem SuperApps An-Nur II</p>
        </div>
    </div>
</body>

</html>