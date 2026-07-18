@extends('kantor.layout')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
    <h2 class="text-2xl font-bold text-[#1B2559]">Syahriah</h2>
    <button onclick="openModal('modal-tambah')"
        class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
        <i class="fa fa-plus mr-2"></i>Buat Tagihan
    </button>
</div>

<div class="card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="tabel-syahriah">
            <thead>
                <tr class="border-b border-gray-100 bg-[#F4F7FE]/50">
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Bebas Tunggakan</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Total Tunggakan</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data akan ditambahkan setelah implementasi backend --}}
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Buat Tagihan --}}
<div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('modal-tambah')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-[#1B2559]">Buat Tagihan</h3>
            <button onclick="closeModal('modal-tambah')" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <p class="text-sm text-[#A3AED0]">Fitur ini belum diimplementasi.</p>
        <div class="mt-4">
            <button type="button" onclick="closeModal('modal-tambah')"
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-xl font-semibold text-sm transition-all">
                Tutup
            </button>
        </div>
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
$(document).ready(function() { $('#tabel-syahriah').DataTable(); });
</script>
@endsection
