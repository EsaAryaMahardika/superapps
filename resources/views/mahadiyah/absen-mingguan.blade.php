@extends('layout')
@section('content')
<div class="body mt-5">
    <div class="mb-3">
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
    <div class="table-responsive">
        <table class="table table-hover js-basic dataTable table-custom spacing5">
            <thead>
                <tr>
                    <th>Kepala Kamar</th>
                    <th>Tidak Jamaah</th>
                    <th>Ghosob</th>
                    <th>Mencuri</th>
                    <th>Ocol</th>
                    <th>Over Gurau</th>
                    <th>Begadang</th>
                    <th>Tidak Roan</th>
                    <th>Bolos Sekolah</th>
                    <th>Merokok</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Rizky Wildan Habibi</td>
                    <td>0</td>
                    <td>10</td>
                    <td>10</td>
                    <td>0</td>
                    <td>10</td>
                    <td>10</td>
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
@endsection

@section('script')

@stop