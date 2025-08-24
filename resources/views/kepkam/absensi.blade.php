@extends('kepkam.layout')
@section('content')
<div class="body mt-5">
    <button class="btn btn-primary" data-toggle="modal" data-target="#add">Buat Absensi</button>
    <div class="m-2">
        {{-- ---------------------------}}
        {{-- <=== Absensi Waqiah ===> --}}
        {{-- ---------------------------}}
        <div class="row">
            <div class="col">
                <h3>Absensi Waqiah</h3>
            </div>
            <p>Tanggal</p>
            <div class="col-2">
                <input id="tanggal" data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
            </div>
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
                    @foreach ($waqiah as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ---------------------------}}
    {{-- <=== Absensi Subuh ===> --}}
    {{-- ---------------------------}}
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Subuh</h3>
            </div>
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
                    @foreach ($subuh as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ---------------------------}}
    {{-- <=== Absensi Dhuhur ===> --}}
    {{-- ---------------------------}}
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Dhuhur</h3>
            </div>
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
                    @foreach ($dhuhur as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ---------------------------}}
    {{-- <=== Absensi Ashar ===> --}}
    {{-- ---------------------------}}
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Ashar</h3>
            </div>
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
                    @foreach ($ashar as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ---------------------------}}
    {{-- <=== Absensi Maghrib ===> --}}
    {{-- ---------------------------}}
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Maghrib</h3>
            </div>
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
                    @foreach ($maghrib as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ---------------------------}}
    {{-- <=== Absensi Isya ===> --}}
    {{-- ---------------------------}}
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Isya</h3>
            </div>
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
                    @foreach ($isya as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Absensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/absen" method="post" class="form-group" id="formAbsensi">
                    @csrf
                    <div class="form-group">
                        <label for="">Jenis Kegiatan</label>
                        <select class="custom-select" name="kegiatan">
                            <option>Pilih kegiatan</option>
                            @foreach ($kegiatan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <input class="form-control" name="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
                    </div>
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Absensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($santri as $item)
                            <tr>
                                <td>{{ $item->nama }} - {{ $item->nis }}</td>
                                <td>
                                    <select name="santri[{{ $item->nis }}]" class="custom-select">
                                        @foreach (['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'] as $kode => $label)
                                            <option value="{{ $kode }}" {{ $kode == 'H' ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection