@extends('kepkam.layout')
@section('content')
<div class="body mt-5">
    <button class="btn btn-primary" data-toggle="modal" data-target="#add">Buat Absensi</button>
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Mingguan</h3>
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
                        <th>Pelanggaran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mingguan as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')}}">
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $item->larangan->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal ?? '-' }}</td>
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
                <h5 class="modal-title">Buat Absensi Mingguan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/kepkam/mingguan" method="post" class="form-group">
                    @csrf
                    <div class="form-group">
                        <label for="">Jenis Pelanggaran</label>
                        <select class="custom-select" name="larangan">
                            <option>Pilih pelanggaran</option>
                            @foreach ($larangan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <input class="form-control" name="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Nama</label>
                        <select name="santri[]" multiple="multiple" class="custom-select santri"></select>
                    </div>
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