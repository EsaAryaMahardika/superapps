@extends('layout')
@section('content')
<div class="body mt-5">
    <div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#add">Tambah Santri Melanggar</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover js-basic dataTable table-custom spacing5" id="pelanggaran">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Pelanggaran</th>
                    <th>Hukuman</th>
                    <th>Tanggal Melanggar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pelanggaran as $item)
                <tr>
                    <td>{{ $item->santri->nama }}</td>
                    <td>{{ $item->larangan->nama }}</td>
                    <td>{{ $item->hukuman }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- Tambah Data Santri Melanggar --}}
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Santri Melanggar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/pelanggaran" method="post" class="form-group">
                    @csrf
                    <div class="mt-2">
                        <label for="">Nama Santri</label>
                        <div class="input-group">
                            <select class="selectpicker" data-live-search="true" name="nis" id="nis" data-size="5" data-width="100%">
                                @foreach ($santri as $item)
                                <option data-tokens="{{ $item->nis }}" value="{{ $item->nis }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Pelanggaran</label>
                        <div class="input-group">
                            <select class="form-control" name="pelanggaran_id">
                                <option value="">Pilih Pelanggaran</option>
                                @foreach ($larangan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Hukuman</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="hukuman" id="hukuman">
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

@section('script')

@stop