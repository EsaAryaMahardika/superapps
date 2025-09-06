@extends('kepkam.layout')
@section('content')
<div class="body mt-5">
    <div class="table-responsive">
        <h3>Jamaah Subuh</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subuh as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Jamaah Dhuhur</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dhuhur as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Waqiah</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waqiah as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Jamaah Ashar</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ashar as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Jamaah Maghrib</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maghrib as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Jamaah Isya</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($isya as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Ngaji Sore</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ngasore as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
                    <td>{{ $item->sakit }}</td>
                    <td>{{ $item->izin }}</td>
                    <td>{{ $item->alfa }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive">
        <h3>Ngaji Malam</h3>
        <table class="table table-hover spacing5 tablenotime">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ngamalam as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->hadir }}</td>
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