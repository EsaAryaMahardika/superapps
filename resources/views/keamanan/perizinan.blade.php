@extends('layout')
@section('content')
<div class="body mt-5">
    <h2>Perizinan</h2>
    <div class="table-responsive">
        <table class="table table-hover spacing5" id="perizinan">
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
                    @if($item->status == 2)
                    <td><button class="btn btn-info izin" data-toggle="modal" data-target="#status" data-nis="{{ $item->santri->nis }}" data-status="{{ $item->status }}">Beri Izin</button></td>
                    @elseif($item->status == 3)
                    <td><button class="btn btn-info lapor" data-nis="{{ $item->santri->nis }}">Lapor Kembali</button></td>
                    @else
                    <td><span class="badge badge-info">{{ $item->statusizin->nama }}</span></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
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
                            <option value="3">Iya</option>
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
    $('#perizinan').DataTable();
    $(document).on('click', '.izin', function() {
        let nis = $(this).data('nis');
        $('#izin').data('nis', nis);
    });
    $(document).on('click', '.lapor', function() {
        let nis = $(this).data('nis');
        $.ajax({
            url: `/lapor/${nis}`,
            method: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",
                status: 5
            },
            success: function(data){
                window.location.href = '/perizinan'
            },
            error: function(xhr) {
                let errorMessage = error.responseJSON?.message || "Terjadi kesalahan tidak diketahui.";
                let errorDetail = error.responseJSON?.error || "";
                alert(`${errorMessage}\n\nDetail: ${errorDetail}`);
            }
        })
    });
    $(document).on('click', '#izin', function() {
        let nis = $(this).data('nis');
        $.ajax({
            url: `/perizinan/${nis}`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: $('#konfirmasi').val()
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