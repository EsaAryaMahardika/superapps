@extends('kepkam.layout')
@section('content')
<div class="body mt-5">
    <div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#add">Buat izin</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover js-basic dataTable table-custom spacing5" id="perizinan">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis Izin</th>
                    <th>Alasan</th>
                    <th>Tanggal Pulang</th>
                    <th>Estimasi Kembali</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perizinan as $item)
                <tr>
                    <td>{{ $item->santri->nama }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->alasanizin->nama }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->berangkat)) }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->es_kembali)) }}</td>
                    @if($item->kembali)
                    <td>{{ date('d-m-Y', strtotime($item->kembali)) }}</td>
                    @else
                    <td>-</td>
                    @endif
                    @if($item->status == 1)
                    <td><button class="btn btn-info izin" data-toggle="modal" data-target="#status" data-nis="{{ $item->santri->nis }}">Beri Izin</button></td>
                    @else
                    <td><span class="badge badge-info">{{ $item->statusizin->nama }}</span></td>
                    @endif
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
                <h5 class="modal-title">Buat Izin</h5>
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
                            <select class="selectpicker" name="nis" id="nis" data-size="5" data-width="100%">
                            <!--<select name="nis" id="nis">-->
                                @foreach ($santri as $item)
                                <option data-tokens="{{ $item->nis }}" value="{{ $item->nis }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Jenis Izin</label>
                        <div class="input-group">
                            <select class="form-control" name="jenis" id="jenis">
                                <option value="" selected>Pilih Jenis Izin</option>
                                <option value="P">Pulang</option>
                                <option value="K">Keluar</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Alasan</label>
                        <div class="input-group">
                            <select class="form-control" name="alasan" id="alasan">
                                @foreach ($alasan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="">Tanggal Kembali</label>
                        <div class="input-group">
                            <input type="date" class="form-control" name="kembali" id="kembali">
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
<div class="modal fade" id="status" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Beri Izin ke Santri?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mt-2">
                    <div class="input-group">
                        <select class="form-control" name="status" id="konfirmasi">
                            <option value="2">Iya</option>
                            <option value="8">Tidak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="izin">Perbarui Izin</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).on('click', '.izin', function() {
        let nis = $(this).data('nis');
        $('#izin').data('nis', nis);
    });
    $(document).on('click', '#izin', function() {
        let nis = $(this).data('nis');
        $.ajax({
            url: `/perizinan/${nis}`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: $('#konfirmasi').val(),
            },
            success: function(data) {
                window.location.href = '/perizinan'
            },
            error: function(xhr) {
                let errorMessage = error.responseJSON?.message || "Terjadi kesalahan tidak diketahui.";
                let errorDetail = error.responseJSON?.error || "";
                alert(`${errorMessage}\n\nDetail: ${errorDetail}`);
            }
        });
    });
</script>
@stop