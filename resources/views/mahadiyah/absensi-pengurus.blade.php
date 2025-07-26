@extends('layout')
@section('content')
<div class="body mt-5">
    <div class="row mb-3">
        <div class="col">
            <button class="btn btn-primary" data-toggle="modal" data-target="#add">Buat Absensi</button>
        </div>
        <div class="col">
            <select name="bulan" id="bulan" class="custom-select">
                <option value="">Pilih Bulan</option>
                <option value="01">Januari</option>
                <option value="02">Februari</option>
                <option value="03">Maret</option>
                <option value="04">April</option>
                <option value="05">Mei</option>
                <option value="06">Juni</option>
                <option value="07">Juli</option>
                <option value="08">Agustus</option>
                <option value="09">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>
        </div>
    </div>
    <div class="container text-center">
        <div class="row">
            <div class="col-4">
                <h3>Absensi Ngaji</h3>
                <div class="table-responsive">
                    <table class="table table-custom js-basic dataTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>S</th>
                                <th>I</th>
                                <th>A</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rizky Wildan Habibi</td>
                                <td>0</td>
                                <td>10</td>
                                <td>10</td>
                            </tr>
                            {{-- @foreach ($pelanggar as $item)
                            <tr>
                                <td>{{ $item->santri->nama }}</td>
                                <td>{{ $item->pelanggaran->nama }}</td>
                                <td>{{ $item->hukuman }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->data->tanggal)) }}</td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-4">
                <h3>Absensi Wirid</h3>
                <div class="table-responsive">
                    <table class="table table-custom js-basic dataTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>S</th>
                                <th>I</th>
                                <th>A</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rizky Wildan Habibi</td>
                                <td>0</td>
                                <td>10</td>
                                <td>10</td>
                            </tr>
                            {{-- @foreach ($pelanggar as $item)
                            <tr>
                                <td>{{ $item->santri->nama }}</td>
                                <td>{{ $item->pelanggaran->nama }}</td>
                                <td>{{ $item->hukuman }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->data->tanggal)) }}</td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-4">
                <h3>Absensi Subuh</h3>
                <div class="table-responsive">
                    <table class="table table-custom js-basic dataTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>S</th>
                                <th>I</th>
                                <th>A</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rizky Wildan Habibi</td>
                                <td>0</td>
                                <td>10</td>
                                <td>10</td>
                            </tr>
                            {{-- @foreach ($pelanggar as $item)
                            <tr>
                                <td>{{ $item->santri->nama }}</td>
                                <td>{{ $item->pelanggaran->nama }}</td>
                                <td>{{ $item->hukuman }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->data->tanggal)) }}</td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
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
                <form action="/absensi-pengurus" method="post" class="form-group" id="formAbsensi">
                    @csrf
                    <div class="form-group">
                        <label for="">Jenis Absensi</label>
                        <select class="custom-select" name="jenis">
                            <option value="N">Ngaji Bandongan</option>
                            <option value="W">Wirid</option>
                            <option value="S">Subuh dan Yasinan</option>
                        </select>
                    </div>
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Absensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rizky Wildan Habibi</td>
                                <td>
                                    <select class="custom-select" name="">
                                        <option value="H" selected>Hadir</option>
                                        <option value="S">Sakit</option>
                                        <option value="I">Izin</option>
                                        <option value="A">Alpa</option>
                                    </select>
                                </td>
                            </tr>
                            {{-- @foreach ($pengurus as $item)
                                <tr>
                                    <td>{{ $item->nama }}</td>
                                    <td>
                                        <select class="custom-select" name="pengurus[{{ $item->nis }}]">
                                            <option value="H">Hadir</option>
                                            <option value="S">Sakit</option>
                                            <option value="I">Izin</option>
                                            <option value="A">Alpa</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach --}}
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