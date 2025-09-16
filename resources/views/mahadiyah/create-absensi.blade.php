@extends('layout')
@section('content')
<div class="mt-5">
    <form action="/mahadiyah/absen-pengurus" method="post" class="form-group" id="formAbsensi">
        @csrf
        <div class="form-group">
            <label for="">Jenis Kegiatan</label>
            <select class="custom-select" name="kegiatan">
                @foreach ($kegiatan as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">Tanggal</label>
            <input class="form-control" name="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
        </div>
        <table class="table table-custom search">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Absensi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengurus as $item)
                <tr>
                    <td>{{ $item->nama }} - {{ $item->nis }}</td>
                    <td>
                        <select name="pengurus[{{ $item->nis }}]" class="custom-select">
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
@endsection