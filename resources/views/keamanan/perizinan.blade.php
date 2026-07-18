@extends('keamanan.layout')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <h2 class="text-2xl font-bold text-[#1B2559]">Perizinan</h2>
</div>

<div class="card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="perizinan">
            <thead>
                <tr class="border-b border-gray-100 bg-[#F4F7FE]/50">
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Jenis Izin</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Alasan</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden md:table-cell">Tgl Pulang</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden md:table-cell">Est. Kembali</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden lg:table-cell">Tgl Kembali</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perizinan as $item)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                    <td class="px-4 py-3 font-medium text-[#2B3674]">{{ $item->santri->nama }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ $item->jenis }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ $item->alasanizin->nama }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden md:table-cell">{{ date('d-m-Y', strtotime($item->berangkat)) }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden md:table-cell">{{ date('d-m-Y', strtotime($item->es_kembali)) }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden lg:table-cell">
                        {{ $item->kembali ? date('d-m-Y', strtotime($item->kembali)) : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($item->status == 2)
                        <button class="izin px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-semibold transition-all"
                            onclick="openModalIzin('{{ $item->santri->nis }}', {{ $item->status }})"
                            data-nis="{{ $item->santri->nis }}" data-status="{{ $item->status }}">
                            Beri Izin
                        </button>
                        @elseif($item->status == 3)
                        <button class="lapor px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-semibold transition-all"
                            data-nis="{{ $item->santri->nis }}">
                            Lapor Kembali
                        </button>
                        @else
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                            {{ $item->statusizin->nama }}
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Beri Izin --}}
<div id="modal-izin" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('modal-izin')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-[#1B2559]">Beri Izin ke Santri?</h3>
            <button onclick="closeModal('modal-izin')" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="mb-5">
            <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Keputusan</label>
            <select class="field-input" id="konfirmasi">
                <option value="3">Iya — Izinkan</option>
                <option value="8">Tidak — Tolak</option>
            </select>
        </div>
        <div class="flex gap-3">
            <button type="button" id="btn-izin"
                class="flex-1 bg-[#4318FF] hover:bg-[#3311CC] text-white py-2.5 rounded-xl font-semibold text-sm transition-all">
                Perbarui Izin
            </button>
            <button type="button" onclick="closeModal('modal-izin')"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-xl font-semibold text-sm transition-all">
                Batal
            </button>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>
let currentNis = null;

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}
function openModalIzin(nis, status) {
    currentNis = nis;
    openModal('modal-izin');
}

document.addEventListener('DOMContentLoaded', function() {
    $('#perizinan').DataTable();

    document.getElementById('btn-izin').addEventListener('click', function() {
        if (!currentNis) return;
        $.ajax({
            url: `/perizinan/${currentNis}`,
            method: 'PUT',
            data: { _token: '{{ csrf_token() }}', status: $('#konfirmasi').val() },
            success: function() { window.location.reload(); },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan.');
            }
        });
    });

    $(document).on('click', '.lapor', function() {
        let nis = $(this).data('nis');
        $.ajax({
            url: `/perizinan/${nis}`,
            method: 'PUT',
            data: { _token: '{{ csrf_token() }}', status: 4 },
            success: function() { window.location.reload(); },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Terjadi kesalahan.');
            }
        });
    });
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('modal-izin'); });
</script>
@endsection
