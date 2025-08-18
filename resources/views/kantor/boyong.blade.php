@extends('layout')
@section('content')
<div class="body mt-5">
    <div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#add">Boyong</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover js-basic dataTable table-custom spacing5">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Kepala Kamar</th>
                    <th>Asrama</th>
                    <th>Alasan Boyong</th>
                    <th>Rencana</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($boyong as $item)
                <tr>
                    <td>{{ $item->nis }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kelas }}</td>
                    {{-- <td>{{ $item->nama }}</td> --}}
                    <td>{{ $item->kepkam->Nama }}</td>
                    <td>{{ $item->asrama->nama }}</td>
                    <td>{{ $item->alasan->keterangan }}</td>
                    <td>{{ $item->rencana->keterangan }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Santri Boyong</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/boyong" method="post" class="form-group">
                    @csrf
                    <div class="mt-2">
                        <label for="">NIS</label>
                        <input class="form-control" type="text" name="nis">
                    </div>
                    <div class="mt-2">
                        <label for="">Nama Santri</label>
                        <input class="form-control" type="text" name="nama">
                    </div>
                    <div class="mt-2">
                        <label for="">Kelas</label>
                        <input class="form-control" type="text" name="kelas">
                    </div>
                    <div class="mt-2">
                        <label for="">Kepala Kamar</label>
                        <select class="form-control select" name="kepkam">
                            @foreach ($kepkam as $item)
                            <option value="{{ $item->NIS }}">{{ $item->Nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="">Asrama</label>
                        <select class="form-control" name="asrama">
                            @foreach ($asrama as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="">Alasan Boyong</label>
                        <select class="form-control" name="alasan">
                            @foreach ($alasan as $item)
                            <option value="{{ $item->id }}">{{ $item->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-2">
                        <label for="">Rencana</label>
                        <select class="form-control" name="rencana">
                            @foreach ($rencana as $item)
                            <option value="{{ $item->id }}">{{ $item->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Input</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection