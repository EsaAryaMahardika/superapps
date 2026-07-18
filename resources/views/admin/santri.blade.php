@extends('admin.layout')
@section('title', 'Data Santri')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Data Santri
        <span class="text-sm font-normal text-[#A3AED0] ml-2">{{ $santri->total() }} santri</span>
    </h2>
</div>

<form method="GET" action="/admin/santri" class="flex gap-3 mb-6 flex-wrap items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS..."
        class="field-input" style="width:220px;">
    <select name="kepkam" class="field-input" style="width:200px;">
        <option value="">Semua Kepkam</option>
        @foreach($kepkams as $k)
            <option value="{{ $k->nis }}" {{ request('kepkam') == $k->nis ? 'selected' : '' }}>
                {{ $k->nama }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-dark">Filter</button>
    @if(request('q') || request('kepkam'))
        <a href="/admin/santri" class="btn btn-light">Reset</a>
    @endif
</form>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase w-10">#</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">NIS</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Kepala Kamar</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Kelas</th>
                <th class="px-4 py-3 w-20"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($santri as $s)
            @php $kp = $kepkams->firstWhere('nis', $s->kepkam); @endphp
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                <td class="px-4 py-3 text-xs text-[#A3AED0]">{{ $santri->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3 text-[#2B3674] font-medium">{{ $s->nama }}</td>
                <td class="px-4 py-3 font-mono text-xs text-[#A3AED0]">{{ $s->nis }}</td>
                <td class="px-4 py-3 text-xs text-[#A3AED0]">
                    @if($s->kepkam)
                        {{ $kp ? $kp->nama : $s->kepkam }}
                    @else
                        <span class="text-orange-400">Belum ada</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-[#A3AED0]">
                    {{ $kp?->jabatan?->divisi?->nama ?? '-' }}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditSantri('{{ $s->nis }}', '{{ addslashes($s->nama) }}', '{{ $s->kepkam }}')"
                            class="w-7 h-7 rounded-lg bg-[#F4F7FE] hover:bg-[#4318FF] text-[#4318FF] hover:text-white transition-all flex items-center justify-center"
                            title="Edit">
                            <i class="fa fa-pen text-xs"></i>
                        </button>
                        <form method="POST" action="/admin/santri/{{ $s->nis }}"
                            onsubmit="return confirm('Hapus santri {{ addslashes($s->nama) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-7 h-7 rounded-lg bg-[#F4F7FE] hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white transition-all flex items-center justify-center"
                                title="Hapus">
                                <i class="fa fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-[#A3AED0] text-sm">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($santri->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm text-[#A3AED0]">
        {{ $santri->links() }}
    </div>
    @endif
</div>

{{-- Modal Edit Santri --}}
<div id="modal-edit-santri" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalSantri()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-4">Edit Santri</h3>
        <form method="POST" id="form-edit-santri" action="">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama</label>
                <input type="text" name="nama" id="edit-santri-nama" required class="field-input">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">NIS</label>
                <input type="text" id="edit-santri-nis-display" class="field-input bg-gray-100 text-gray-400" disabled>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Kepala Kamar</label>
                <select name="kepkam" id="edit-santri-kepkam" class="field-input">
                    <option value="">— Belum ada —</option>
                    @foreach($kepkams as $k)
                    <option value="{{ $k->nis }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                <button type="button" onclick="closeModalSantri()" class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
function openEditSantri(nis, nama, kepkam) {
    document.getElementById('form-edit-santri').action = '/admin/santri/' + nis;
    document.getElementById('edit-santri-nama').value = nama;
    document.getElementById('edit-santri-nis-display').value = nis;
    document.getElementById('edit-santri-kepkam').value = kepkam || '';
    document.getElementById('modal-edit-santri').classList.remove('hidden');
    document.getElementById('modal-edit-santri').classList.add('flex');
}
function closeModalSantri() {
    document.getElementById('modal-edit-santri').classList.add('hidden');
    document.getElementById('modal-edit-santri').classList.remove('flex');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModalSantri(); });
</script>
@endsection
