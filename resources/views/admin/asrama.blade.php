@extends('admin.layout')
@section('title', 'Data Asrama')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Data Asrama</h2>
    <button onclick="openModal('modal-tambah')" class="btn btn-primary">
        <i class="fa fa-plus"></i> Tambah Asrama
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($asrama as $a)
    <div class="card">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-[#1B2559]">{{ $a->nama }}</h3>
                <p class="text-xs text-[#A3AED0] mt-0.5">{{ $a->kamar_count }} kamar</p>
            </div>
            <a href="/admin/asrama/{{ $a->id }}/kamar" class="text-xs text-[#4318FF] hover:underline font-medium shrink-0">
                Lihat Kamar →
            </a>
        </div>
        <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
            <button onclick="openEditAsrama({{ $a->id }}, '{{ addslashes($a->nama) }}')"
                class="text-xs text-[#4318FF] hover:underline font-medium flex items-center gap-1">
                <i class="fa fa-pen text-[10px]"></i> Edit
            </button>
            <form method="POST" action="/admin/asrama/{{ $a->id }}"
                onsubmit="return confirm('Hapus asrama {{ addslashes($a->nama) }}? Pastikan tidak ada kamar di dalamnya.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-500 hover:underline font-medium flex items-center gap-1">
                    <i class="fa fa-trash text-[10px]"></i> Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-8 text-[#A3AED0] text-sm">Belum ada asrama.</div>
    @endforelse
</div>

{{-- Modal Tambah Asrama --}}
<div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('modal-tambah')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-4">Tambah Asrama</h3>
        <form method="POST" action="/admin/asrama">
            @csrf
            <input type="text" name="nama" required placeholder="Nama asrama" class="field-input mb-4">
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                <button type="button" onclick="closeModal('modal-tambah')" class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Asrama --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModal('modal-edit')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-4">Edit Asrama</h3>
        <form method="POST" id="form-edit-asrama" action="">
            @csrf @method('PUT')
            <input type="text" name="nama" id="edit-nama-asrama" required placeholder="Nama asrama" class="field-input mb-4">
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                <button type="button" onclick="closeModal('modal-edit')" class="btn btn-light flex-1">Batal</button>
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
function openEditAsrama(id, nama) {
    document.getElementById('form-edit-asrama').action = '/admin/asrama/' + id;
    document.getElementById('edit-nama-asrama').value = nama;
    openModal('modal-edit');
}
</script>
@endsection
