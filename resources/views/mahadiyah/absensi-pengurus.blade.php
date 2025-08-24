@extends('layout')
@section('content')
<div class="body mt-5">
    <div class="row">
        <div class="col-6">
            <a class="btn btn-primary" href="/mahadiyah/absen-pengurus">Buat Absensi</a>
        </div>
        <div class="col-6">
            <p>Tanggal</p>
            <input id="tanggal" data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
        </div>
    </div>
    <div class="m-2">
        {{---------------------}}
        {{-- ABSENSI YASINAN --}}
        {{---------------------}}
        <div class="col">
            <h3>Absensi Yasinan</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover spacing5 tabel">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabel = [
                            'H' => 'Hadir',
                            'S' => 'Sakit',
                            'I' => 'Izin',
                            'A' => 'Alpa',
                        ];
                    @endphp
                    @foreach ($yasinan as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->pengurus->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-----------------------}}
        {{-- ABSENSI BANDONGAN --}}
        {{-----------------------}}
        <h3>Absensi Bandongan</h3>
        <div class="table-responsive">
            <table class="table table-hover spacing5 tabel">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabel = [
                            'H' => 'Hadir',
                            'S' => 'Sakit',
                            'I' => 'Izin',
                            'A' => 'Alpa',
                        ];
                    @endphp
                    @foreach ($bandongan as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->pengurus->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-----------------------}}
        {{-- ABSENSI wIRID --}}
        {{-----------------------}}
        <h3>Absensi Wirid</h3>
        <div class="table-responsive">
            <table class="table table-hover spacing5 tabel">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabel = [
                            'H' => 'Hadir',
                            'S' => 'Sakit',
                            'I' => 'Izin',
                            'A' => 'Alpa',
                        ];
                    @endphp
                    @foreach ($wirid as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->pengurus->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection