@extends('admin.layout')
@section('title', 'Data Pengurus')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
    <h2 class="text-2xl font-bold text-[#1B2559]">Data Pengurus</h2>
    <div class="flex flex-wrap gap-2">
        <a href="/admin/pengurus/template"
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
    </div>
</div>

{{-- Flash import errors --}}
@if(session('import_errors'))
<div class="card mb-4 border border-amber-200 bg-amber-50">
    <p class="text-xs font-semibold text-amber-700 mb-2"><i class="fa fa-triangle-exclamation mr-1"></i>Beberapa baris dilewati:</p>
    <ul class="text-xs text-amber-600 space-y-0.5 list-disc list-inside">
        @foreach(session('import_errors') as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="GET" action="/admin/pengurus" class="flex gap-3 mb-6 flex-wrap items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS..."
        class="field-input" style="width:220px;">
    <button type="submit" class="btn btn-dark">Cari</button>
    @if(request('q'))
        <a href="/admin/pengurus" class="btn btn-light">Reset</a>
    @endif
</form>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">NIS</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Jabatan</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Divisi</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengurus as $p)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                <td class="px-4 py-3 font-mono text-xs text-[#2B3674]">{{ $p->nis }}</td>
                <td class="px-4 py-3 text-[#2B3674]">{{ $p->nama }}</td>
                <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ $p->jabatan->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ $p->jabatan->divisi->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="/admin/pengurus/{{ $p->nis }}/edit" class="text-xs text-[#4318FF] hover:underline">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-[#A3AED0] text-sm">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($pengurus->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm text-[#A3AED0]">
        {{ $pengurus->links() }}
    </div>
    @endif
</div>

{{-- Modal Import CSV --}}
<div id="modal-import" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalImport()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold text-[#1B2559] mb-1">Import Pengurus dari CSV</h3>
        <p class="text-xs text-[#A3AED0] mb-4">Format: <span class="font-mono">nis, nama, jabatan_id</span> — baris pertama adalah header. Kolom <span class="font-mono">jabatan_id</span> boleh kosong.</p>
        <form method="POST" action="/admin/pengurus/import" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Pilih File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" required
                    class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#4318FF]/10 file:text-[#4318FF] hover:file:bg-[#4318FF]/20 transition-all">
                <p class="text-[11px] text-[#A3AED0] mt-1.5">Maks. 2MB. Jabatan bisa diatur di halaman <a href="/mahadiyah/pengurus" class="text-[#4318FF] underline">Data Pengurus</a> setelah import.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Import</button>
                <button type="button" onclick="closeModalImport()" class="btn btn-light flex-1">Batal</button>
            </div>
        </form>
        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
            <a href="/admin/pengurus/template" class="text-xs text-[#4318FF] hover:underline">
                <i class="fa fa-download mr-1"></i> Download template CSV
            </a>
        </div>
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
                    <h3 class="text-base font-bold text-[#1B2559]">Tutorial Import CSV Pengurus</h3>
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
                    <p class="text-sm font-semibold text-[#1B2559]">Isi Data Pengurus</p>
                    <p class="text-xs text-[#A3AED0] mt-0.5">Kolom <code class="bg-gray-100 px-1 rounded text-[#4318FF]">nis</code> = NIS pengurus, <code class="bg-gray-100 px-1 rounded text-[#4318FF]">nama</code> = nama lengkap, <code class="bg-gray-100 px-1 rounded text-[#4318FF]">jabatan_id</code> = ID jabatan (lihat di bagian bawah template CSV, boleh kosong).</p>
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
            <div class="flex gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                <span class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">!</span>
                <div>
                    <p class="text-sm font-semibold text-amber-700">Perhatian</p>
                    <p class="text-xs text-amber-600 mt-0.5">Format kolom NIS sebagai <strong>Text</strong> di Excel agar angka awal (leading zero) tidak hilang. Daftar <strong>jabatan_id</strong> tersedia di bagian bawah file template CSV yang didownload.</p>
                </div>
            </div>
        </div>
        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center">
            <a href="/admin/pengurus/template" class="text-xs text-[#4318FF] hover:underline flex items-center gap-1">
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
    document.getElementById('modal-import').classList.remove('hidden');
    document.getElementById('modal-import').classList.add('flex');
}
function closeModalImport() {
    document.getElementById('modal-import').classList.add('hidden');
    document.getElementById('modal-import').classList.remove('flex');
}
function openModalTutorial() {
    document.getElementById('modal-tutorial').classList.remove('hidden');
    document.getElementById('modal-tutorial').classList.add('flex');
}
function closeModalTutorial() {
    document.getElementById('modal-tutorial').classList.add('hidden');
    document.getElementById('modal-tutorial').classList.remove('flex');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeModalImport(); closeModalTutorial(); }
});
</script>
@endsection
