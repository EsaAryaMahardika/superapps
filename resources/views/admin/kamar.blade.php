@extends('admin.layout')
@section('title', 'Kamar — ' . $asrama->nama)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="/admin/asrama" class="text-[#A3AED0] hover:text-[#2B3674]"><i class="fa fa-arrow-left"></i></a>
    <div>
        <h2 class="text-2xl font-bold text-[#1B2559]">{{ $asrama->nama }}</h2>
        <p class="text-sm text-[#A3AED0]">{{ $kamar->count() }} kamar</p>
    </div>
    <button onclick="document.getElementById('modal-tambah').classList.remove('hidden'); document.getElementById('modal-tambah').classList.add('flex')"
        class="btn btn-primary ml-auto">
        <i class="fa fa-plus"></i> Tambah Kamar
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($kamar as $k)
    <div class="card">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-[#1B2559]">{{ $k->nama }}</h3>
                <p class="text-xs text-[#A3AED0] mt-0.5">
                    {{ $k->kepkam ? $k->kepkam->nama : 'Belum ada kepkam' }}
                    @if($k->kepkam?->jabatan?->divisi)
                        <span class="text-[#4318FF]">— {{ $k->kepkam->jabatan->divisi->nama }}</span>
                    @endif
                </p>
            </div>
            <a href="/admin/asrama/{{ $asrama->id }}/kamar/{{ $k->id }}/santri"
                class="text-xs text-[#4318FF] hover:underline font-medium shrink-0">Santri →</a>
        </div>
        <div class="flex gap-3">
            <button onclick="openEditModal({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ $k->kepkam_nis }}')"
                class="text-xs text-[#4318FF] hover:underline" style="background:none;border:none;cursor:pointer;">Edit</button>
            <form method="POST" action="/admin/asrama/{{ $asrama->id }}/kamar/{{ $k->id }}"
                onsubmit="return confirm('Hapus kamar ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-500 hover:underline" style="background:none;border:none;cursor:pointer;">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-8 text-[#A3AED0] text-sm">Belum ada kamar.</div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('modal-tambah').classList.add('hidden'); document.getElementById('modal-tambah').classList.remove('flex')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-4">Tambah Kamar</h3>
        <form method="POST" action="/admin/asrama/{{ $asrama->id }}/kamar">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Kepala Kamar</label>
                <select name="kepkam_nis" required class="field-input">
                    <option value="">-- Pilih Kepala Kamar --</option>
                    @foreach($kepkamList as $kp)
                    <option value="{{ $kp->nis }}">{{ $kp->nama }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-[#A3AED0] mt-1">Nama kamar otomatis ikut nama kepala kamar.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden'); document.getElementById('modal-tambah').classList.remove('flex')"
                    class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('modal-edit').classList.add('hidden'); document.getElementById('modal-edit').classList.remove('flex')"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-4">Edit Kamar</h3>
        <form id="form-edit" method="POST" action="">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Kepala Kamar</label>
                <select name="kepkam_nis" id="edit-kepkam" required class="field-input">
                    <option value="">-- Pilih Kepala Kamar --</option>
                    @foreach($kepkamList as $kp)
                    <option value="{{ $kp->nis }}">{{ $kp->nama }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-[#A3AED0] mt-1">Nama kamar otomatis ikut nama kepala kamar.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden'); document.getElementById('modal-edit').classList.remove('flex')"
                    class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
function openEditModal(id, nama, kepkamNis) {
    document.getElementById('form-edit').action = '/admin/asrama/{{ $asrama->id }}/kamar/' + id;
    document.getElementById('edit-kepkam').value = kepkamNis || '';
    document.getElementById('modal-edit').classList.remove('hidden');
    document.getElementById('modal-edit').classList.add('flex');
}
</script>
@endsection
