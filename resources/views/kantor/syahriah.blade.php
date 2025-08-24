@extends('layout')
@section('content')
<div class="body mt-5">
    <div>
        <button class="btn btn-primary m-3" data-toggle="modal" data-target="#add">Buat Tagihan</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover spacing5 tabel">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Bebas Tunggakan</th>
                    <th>Total Tunggakan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($pelanggaran as $item)
                <tr>
                    <td>{{ $item->santri->nama }}</td>
                    @if ($item->tunggakan == 0)
                        <td>Ya</td>
                    @else
                        <td>Tidak</td>
                    @endif
                    <td>{{ $item->total }}</td>
                    <td></td>
                </tr>
                @endforeach --}}
            </tbody>
        </table>
    </div>
</div>
@endsection