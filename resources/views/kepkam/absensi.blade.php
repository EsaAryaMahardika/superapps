@extends('kepkam.layout')
@section('content')
<div class="body mt-5">
    <button class="btn btn-primary" data-toggle="modal" data-target="#add">Buat Absensi</button>
    <div class="m-2">
        <div class="row">
            <div class="col">
                <h3>Absensi Waqiah</h3>
            </div>
            <p>Tanggal</p>
            <div class="col-2">
                <input id="tanggal" data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-custom spacing5" id="waqiah">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusLabel = [
                            'H' => 'Hadir',
                            'S' => 'Sakit',
                            'I' => 'Izin',
                            'A' => 'Alpa',
                        ];
                    @endphp
                    @foreach ($waqiah as $item)
                        <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y')>
                            <td>{{ $item->santri->nama ?? "-" }}</td>
                            <td>{{ $statusLabel[$item->status] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- <div class="row">
            <div class="col">
                <h3>Absensi Jamaah</h3>
            </div>
            <p>Tanggal</p>
            <div class="col-2">
                <input data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-custom js-basic dataTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Subuh</th>
                        <th>Dhuhur</th>
                        <th>Ashar</th>
                        <th>Maghrib</th>
                        <th>Isya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pelanggar as $item)
                    <tr>
                        <td>{{ $item->santri->nama }}</td>
                        <td>{{ $item->pelanggaran->nama }}</td>
                        <td>{{ $item->hukuman }}</td>
                        <td>{{ date('d-m-Y', strtotime($item->data->tanggal)) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr> --}}
        {{-- <div class="row">
            <div class="col">
                <h3>Absensi Mingguan</h3>
            </div>
            <p>Pekan</p>
            <div class="col-1">
                <select name="pekan" id="pekan" class="custom-select">
                    <option value=""></option>
                    <option value="01">1</option>
                    <option value="01">2</option>
                    <option value="01">3</option>
                    <option value="01">4</option>
                    <option value="01">5</option>
                    <option value="01">6</option>
                    <option value="01">7</option>
                    <option value="01">8</option>
                    <option value="01">9</option>
                    <option value="01">10</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-custom js-basic dataTable">
                <thead>
                    <tr>
                        <th>Pelanggaran</th>
                        <th>S</th>
                        <th>I</th>
                        <th>A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pelanggar as $item)
                    <tr>
                        <td>{{ $item->santri->nama }}</td>
                        <td>{{ $item->pelanggaran->nama }}</td>
                        <td>{{ $item->hukuman }}</td>
                        <td>{{ date('d-m-Y', strtotime($item->data->tanggal)) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Absensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/absen" method="post" class="form-group" id="formAbsensi">
                    @csrf
                    <div class="form-group">
                        <label for="">Jenis Absensi</label>
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
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Absensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($santri as $item)
                            <tr>
                                <td>{{ $item->nama }} - {{ $item->nis }}</td>
                                <td>
                                    <select name="santri[{{ $item->nis }}]" class="custom-select">
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
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>
var minDateFilter = "";
var oTable = $('#waqiah').DataTable();
$('#tanggal').datepicker({
    format: 'dd/mm/yyyy',
    autoclose: true
  }).on('change', function () {
    let val = $(this).val();
    if (val) {
      let parts = val.split('/');
      minDateFilter = new Date(parts[2], parts[1] - 1, parts[0]).getTime();
    } else {
      minDateFilter = "";
    }
    oTable.draw();
  });

$.fn.dataTableExt.afnFiltering.push(
  function(oSettings, aData, iDataIndex) {
    // Ambil tanggal dari atribut data pada baris
    let row = oSettings.aoData[iDataIndex].nTr;
    let tanggalStr = $(row).data('tanggal');
    if (!tanggalStr) return false;

    let parts = tanggalStr.split('/');
    let rowDate = new Date(parts[2], parts[1] - 1, parts[0]).getTime(); // dd/mm/yyyy

    if (minDateFilter && !isNaN(minDateFilter)) {
      if (rowDate != minDateFilter) {
        return false;
      }
    }

    return true;
  }
);
</script>
@endsection