@extends('layout')
@section('content')
<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $asrama }}</h3> --}}
                <div>Asrama</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $santri }}</h3> --}}
                <div>Total Santri Putra</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $pengurus }}</h3> --}}
                <div>Total Pengurus</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $kepkam }}</h3> --}}
                <div>Total Kepala Kamar</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $nokepkam }}</h3> --}}
                <div>Total Non Kepala Kamar</div>
            </div>
        </div>
    </div>
</div>
<p>Tanggal</p>
<div class="col-6">
    <input id="tanggal" data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
</div>
<div class="table-responsive">
    <h3>Jamaah Subuh</h3>
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subuh as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dhuhur as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($waqiah as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ashar as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maghrib as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($isya as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ngasore as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
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
    <table class="table table-hover spacing5 tabel">
        <thead>
            <tr>
                <th>Kepala Kamar</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ngamalam as $item)
            <tr data-tanggal="{{ $item->tanggal }}">
                <td>{{ $item->nama }}</td>
                <td>{{ $item->hadir }}</td>
                <td>{{ $item->sakit }}</td>
                <td>{{ $item->izin }}</td>
                <td>{{ $item->alfa }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection