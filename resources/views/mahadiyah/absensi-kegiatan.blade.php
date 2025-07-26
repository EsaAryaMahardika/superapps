@extends('layout')
@section('content')
<div class="body mt-5">
    <div class="table-responsive">
        <h3>Waqiah</h3>
        <table class="table table-hover js-basic dataTable table-custom spacing5">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waqiah as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection