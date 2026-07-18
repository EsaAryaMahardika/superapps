@extends('admin.layout')
@section('title', 'Data Santri')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
    <h2 class="text-2xl font-bold text-[#1B2559]">Data Santri
        <span class="text-sm font-normal text-[#A3AED0] ml-2">{{ $totalSantri }} santri</span>
    </h2>
    <div class="flex flex-wrap gap-2">
        <a href="/admin/santri/template"
            class="btn bg-white border border-gray-200 text-[#2B3674] hover:bg-gray-50 text-sm py-2 px-4 flex items-center gap-2">
            <i class="fa fa-download text-green-600"></i> Template CSV
        </a>
        <button onclick="openModalTutorial()"
            class="btn bg-white border border-gray-200 text-[#2B3674] hover:bg-gray-50 text-sm py-2 px-4 flex items-center gap-2">
            <i class="fa fa-circle-info text-blue-500"></i> Tutorial
        </button>
        <button onclick="openModalImport()"
            class="btn bg-white border border-gray-200 text-[#2B3674] hover:bg-gray-50 text-sm py-2 px-4 flex items-center gap-2">
            <i class="fa fa-file-import text-blue-600"></i> Import CSV
        </button>
        <button onclick="openModalTambah()" class="btn btn-primary text-sm py-2 px-4">
            <i class="fa fa-plus"></i> Tambah Santri
        </button>
    </div>
</div>

{{-- Filter --}}
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

@if($grouped->isEmpty())
<div class="card text-center py-10 text-[#A3AED0]">
    <i class="fa fa-inbox text-3xl mb-2 block opacity-30"></i>
    <p class="text-sm">Tidak ada data santri.</p>
</div>
@else

{{-- List per Kepala Kamar --}}
@foreach($grouped as $kepkamNis => $santriList)
@php $kp = $kepkams->get($kepkamNis); @endphp

<div class="mb-6">
    {{-- Header Kepala Kamar --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-[#4318FF]/10 flex items-center justify-center shrink-0">
                <i class="fa fa-house text-[#4318FF] text-xs"></i>
            </div>
            <div>
                @if($kp)
                    <h3 class="font-bold text-[#1B2559] text-sm">{{ $kp->nama }}</h3>
                    <p class="text-[11px] text-[#A3AED0]">{{ $kp->jabatan?->divisi?->nama ?? '-' }}</p>
                @else
                    <h3 class="font-bold text-orange-500 text-sm">Belum ada Kepala Kamar</h3>
                    <p class="text-[11px] text-[#A3AED0]">Santri belum diassign ke kepala kamar</p>
                @endif
            </div>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#F4F7FE] text-[#4318FF]">
            {{ $santriList->count() }} santri
        </span>
    </div>

    {{-- Tabel Santri --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-[#F4F7FE]/50">
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase w-10">#</th>
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase">NIS</th>
                    <th class="px-4 py-2.5 w-20"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($santriList as $i => $s)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                    <td class="px-4 py-2.5 text-xs text-[#A3AED0]">{{ $i + 1 }}</td>
                    <td class="px-4 py-2.5 text-[#2B3674] font-medium text-sm">{{ $s->nama }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs text-[#A3AED0]">{{ $s->nis }}</td>
                    <td class="px-4 py-2.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <button onclick="openEditSantri('{{ $s->nis }}', '{{ addslashes($s->nama) }}', '{{ $s->kepkam }}')"
                                class="w-7 h-7 rounded-lg bg-[#F4F7FE] hover:bg-[#4318FF] text-[#4318FF] hover:text-white transition-all flex items-center justify-center"
                                title="Edit">
                                <i class="fa fa-pen text-[10px]"></i>
                            </button>
                            <form method="POST" action="/admin/santri/{{ $s->nis }}"
                                onsubmit="return confirm('Hapus santri {{ addslashes($s->nama) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-7 h-7 rounded-lg bg-[#F4F7FE] hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white transition-all flex items-center justify-center"
                                    title="Hapus">
                                    <i class="fa fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endif

{{-- Modal Import CSV --}}
<div id="modal-import-santri" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalImport()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-1">Import Santri dari CSV</h3>
        <p class="text-xs text-[#A3AED0] mb-4">Format: <span class="font-mono">nis, nama, kepkam</span> — baris pertama adalah header.</p>
        <form method="POST" action="/admin/santri/import" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Pilih File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" required
                    class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#4318FF]/10 file:text-[#4318FF] hover:file:bg-[#4318FF]/20 transition-all">
                <p class="text-[11px] text-[#A3AED0] mt-1.5">Maks. 2MB. Kolom <span class="font-mono">kepkam</span> boleh kosong.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Import</button>
                <button type="button" onclick="closeModalImport()" class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
            <a href="/admin/santri/template" class="text-xs text-[#4318FF] hover:underline">
                <i class="fa fa-download mr-1"></i> Download template CSV
            </a>
        </div>
    </div>
</div>

{{-- Modal Tambah Santri --}}
<div id="modal-tambah-santri" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalTambah()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-4">Tambah Santri</h3>
        <form method="POST" action="/admin/santri">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">NIS</label>
                <input type="text" name="nis" required placeholder="Nomor Induk Santri" class="field-input">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama</label>
                <input type="text" name="nama" required placeholder="Nama lengkap santri" class="field-input">
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Kepala Kamar</label>
                <select name="kepkam" class="field-input">
                    <option value="">— Belum ada —</option>
                    @foreach($kepkams as $k)
                    <option value="{{ $k->nis }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
                <button type="button" onclick="closeModalTambah()" class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
    </div>
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

{{-- Modal Tutorial --}}
<div id="modal-tutorial" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalTutorial()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-lg z-10 p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i class="fa fa-circle-info text-blue-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-[#1B2559]">Tutorial Import CSV</h3>
                    <p class="text-xs text-[#A3AED0]">Cara edit file CSV dengan Excel lalu import ke sistem</p>
                </div>
            </div>
            <button onclick="closeModalTutorial()" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-[#4318FF] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                <div>
                    <p class="text-sm font-semibold text-[#1B2559]">Download Template</p>
                    <p class="text-xs text-[#A3AED0] mt-0.5">Klik tombol <strong class="text-[#2B3674]">Template CSV</strong> untuk download file template.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-[#4318FF] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                <div>
                    <p class="text-sm font-semibold text-[#1B2559]">Buka di Excel</p>
                    <p class="text-xs text-[#A3AED0] mt-0.5">Buka Excel → <strong class="text-[#2B3674]">File → Open</strong> → <strong class="text-[#2B3674]">Blok Kolom A</strong> → pilih Menu <strong class="text-[#2B3674]">Data → Text to Columns</strong>, pilih <strong class="text-[#2B3674]">Delimited → Comma → Next sampai Finish</strong>.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-[#4318FF] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                <div>
                    <p class="text-sm font-semibold text-[#1B2559]">Isi Data Santri</p>
                    <p class="text-xs text-[#A3AED0] mt-0.5">Kolom <code class="bg-gray-100 px-1 rounded text-[#4318FF]">nis</code> = NIS santri, <code class="bg-gray-100 px-1 rounded text-[#4318FF]">nama</code> = nama lengkap, <code class="bg-gray-100 px-1 rounded text-[#4318FF]">kepkam</code> = NIS kepala kamar. Cek NIS Kepala Kamar di Data Pengurus.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-[#4318FF] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">4</span>
                <div>
                    <p class="text-sm font-semibold text-[#1B2559]">Save as CSV</p>
                    <p class="text-xs text-[#A3AED0] mt-0.5"><strong class="text-[#2B3674]">File → Save As</strong> → pilih format <strong class="text-[#2B3674]">CSV (Comma delimited)</strong> → Save → Yes.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="w-6 h-6 rounded-full bg-[#4318FF] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">5</span>
                <div>
                    <p class="text-sm font-semibold text-[#1B2559]">Import ke Sistem</p>
                    <p class="text-xs text-[#A3AED0] mt-0.5">Klik <strong class="text-[#2B3674]">Import CSV</strong>, pilih file CSV yang sudah diisi, lalu klik Import.</p>
                </div>
            </div>
            
        </div>
        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center">
            <a href="/admin/santri/template" class="text-xs text-[#4318FF] hover:underline flex items-center gap-1">
                <i class="fa fa-download"></i> Download template CSV
            </a>
            <button onclick="closeModalTutorial()" class="btn btn-dark py-2 px-5 text-sm">Tutup</button>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function openModalImport() {
    document.getElementById('modal-import-santri').classList.remove('hidden');
    document.getElementById('modal-import-santri').classList.add('flex');
}
function closeModalImport() {
    document.getElementById('modal-import-santri').classList.add('hidden');
    document.getElementById('modal-import-santri').classList.remove('flex');
}
function openModalTutorial() {
    document.getElementById('modal-tutorial').classList.remove('hidden');
    document.getElementById('modal-tutorial').classList.add('flex');
}
function closeModalTutorial() {
    document.getElementById('modal-tutorial').classList.add('hidden');
    document.getElementById('modal-tutorial').classList.remove('flex');
}
function openModalTambah() {
    document.getElementById('modal-tambah-santri').classList.remove('hidden');
    document.getElementById('modal-tambah-santri').classList.add('flex');
}
function closeModalTambah() {
    document.getElementById('modal-tambah-santri').classList.add('hidden');
    document.getElementById('modal-tambah-santri').classList.remove('flex');
}
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
