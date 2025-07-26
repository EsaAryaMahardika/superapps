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
    <h3>Jurnal Pengajar</h3>
    <div class="table-responsive">
        <table class="table table-custom js-basic dataTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Materi</th>
                    <th>Status</th>
                    <th>Badal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Rizky Wildan Habibi</td>
                    <td>0</td>
                    <td>10</td>
                    <td>10</td>
                    <td>Hanafi</td>
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
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Jurnal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/pelanggaran" method="post" class="form-group">
                    @csrf
                    <div class="mt-2">
                        <label for="">Nama Pengajar</label>
                        <div class="input-group">
                            <select class="selectpicker" data-live-search="true" name="nip" id="nip" data-size="5" data-width="100%">
                                {{-- @foreach ($santri as $item)
                                <option data-tokens="{{ $item->nis }}">{{ $item->nama }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Kelas</label>
                        <div class="input-group">
                            <select class="selectpicker" data-live-search="true" name="kelas_id" id="kelas_id" data-size="5" data-width="100%">
                                {{-- @foreach ($santri as $item)
                                <option data-tokens="{{ $item->nis }}">{{ $item->nama }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Tanggal</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="tanggal" id="tanggal">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Materi</label>
                        <div class="input-group">
                            <input type="textarea" class="form-control" name="tanggal" id="tanggal">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Status</label>
                        <div class="input-group">
                            <select class="form-control" name="jenis" id="jenis">
                                <option value="H" selected>Hadir</option>
                                <option value="S">Sakit</option>
                                <option value="I">Izin</option>
                                <option value="A">Alpa</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Badal</label>
                        <div class="input-group">
                            <select class="selectpicker" data-live-search="true" name="badal" id="badal" data-size="5" data-width="100%">
                                {{-- @foreach ($santri as $item)
                                <option data-tokens="{{ $item->nis }}">{{ $item->nama }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection