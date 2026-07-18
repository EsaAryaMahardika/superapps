@extends('kantor.layout')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <h2 class="text-2xl font-bold text-[#1B2559]">Boyong</h2>
    <button onclick="openModal('modal-tambah')"
        class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
        <i class="fa fa-plus mr-2"></i>Tambah Boyong
    </button>
</div>

<div class="card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="tabel-boyong">
            <thead>
                <tr class="border-b border-gray-100 bg-[#F4F7FE]/50">
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">NIS</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden sm:table-cell">Kelas</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden md:table-cell">Kepala Kamar</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden md:table-cell">Alasan</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase hidden lg:table-cell">Rencana</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($boyong as $item)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                    <td class="px-4 py-3 font-mono text-xs text-[#A3AED0]">{{ $item->nis }}</td>
                    <td class="px-4 py-3 font-medium text-[#2B3674]">{{ $item->nama }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden sm:table-cell">{{ $item->kelas }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden md:table-cell">{{ $item->kepkam->nama }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden md:table-cell">{{ $item->alasan->keterangan }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs hidden lg:table-cell">{{ $item->rencana->keterangan }}</td>
                    <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Boyong --}}
<div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('modal-tambah')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-md z-10 p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-[#1B2559]">Santri Boyong</h3>
            <button onclick="closeModal('modal-tambah')" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form action="/boyong" method="post">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">NIS</label>
                <input class="field-input" type="text" name="nis" required placeholder="Nomor Induk Santri">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama Santri</label>
                <input class="field-input" type="text" name="nama" required placeholder="Nama lengkap">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Kelas</label>
                <input class="field-input" type="text" name="kelas" required placeholder="Kelas">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Alasan Boyong</label>
                <select class="field-input" name="alasan" required>
                    <option value="">Pilih Alasan</option>
                    @foreach ($alasan as $item)
                    <option value="{{ $item->id }}">{{ $item->keterangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Rencana</label>
                <select class="field-input" name="rencana" required>
                    <option value="">Pilih Rencana</option>
                    @foreach ($rencana as $item)
                    <option value="{{ $item->id }}">{{ $item->keterangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-[#4318FF] hover:bg-[#3311CC] text-white py-2.5 rounded-xl font-semibold text-sm transition-all">Input</button>
                <button type="button" onclick="closeModal('modal-tambah')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-xl font-semibold text-sm transition-all">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('script')
<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal('modal-tambah'); });
$(document).ready(function() { $('#tabel-boyong').DataTable(); });
</script>
@endsection
