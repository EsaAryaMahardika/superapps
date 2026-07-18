@extends('mahadiyah.layout')

@section('content')
<div class="mt-2 sm:mt-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#1B2559]">Data Santri
                <span class="text-sm font-normal text-[#A3AED0] ml-2">{{ $totalSantri }} santri</span>
            </h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/mahadiyah/santri/template"
                class="bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] px-2.5 py-2 rounded-xl font-semibold transition-all text-sm flex items-center gap-1.5">
                <i class="fa fa-download text-green-600 text-xs"></i>
                <span class="hidden sm:inline">Template CSV</span>
            </a>
            <button onclick="openModalTutorial()"
                class="bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] px-2.5 py-2 rounded-xl font-semibold transition-all text-sm flex items-center gap-1.5">
                <i class="fa fa-circle-info text-blue-500 text-xs"></i>
                <span class="hidden sm:inline">Tutorial</span>
            </button>
            <button onclick="openModalImport()"
                class="bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] px-2.5 py-2 rounded-xl font-semibold transition-all text-sm flex items-center gap-1.5">
                <i class="fa fa-file-import text-blue-600 text-xs"></i>
                <span class="hidden sm:inline">Import CSV</span>
            </button>
            <button onclick="openModalTambah()"
                class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm flex items-center gap-1.5">
                <i class="fa fa-plus"></i>
                <span class="hidden sm:inline">Tambah Santri</span>
            </button>
        </div>
    </div>

    {{-- Flash import errors --}}
    @if(session('import_errors'))
    <div class="card mb-4" style="border:1px solid #fcd34d; background:#fffbeb;">
        <p class="text-xs font-semibold text-amber-700 mb-2"><i class="fa fa-triangle-exclamation mr-1"></i>Beberapa baris dilewati:</p>
        <ul class="text-xs text-amber-600 space-y-0.5 list-disc list-inside">
            @foreach(session('import_errors') as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="/mahadiyah/santri" class="flex flex-col sm:flex-row gap-2 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS..."
            class="w-full sm:w-56 bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-11 px-4 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all">
        <select name="kepkam"
            class="w-full sm:w-52 bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-11 px-4 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all">
            <option value="">Semua Kepkam</option>
            @foreach($kepkams as $k)
                <option value="{{ $k->nis }}" {{ request('kepkam') == $k->nis ? 'selected' : '' }}>
                    {{ $k->nama }}
                </option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit"
                class="px-4 h-11 rounded-xl text-sm font-semibold text-white transition-all" style="background:#1B2559;">
                <i class="fa fa-search mr-1"></i> Filter
            </button>
            @if(request('q') || request('kepkam'))
                <a href="/mahadiyah/santri"
                    class="px-4 h-11 rounded-xl text-sm font-semibold flex items-center border border-gray-200 bg-white transition-all" style="color:#2B3674;">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- List per Kepala Kamar --}}
    @if($grouped->isEmpty())
    <div class="card text-center py-10 text-[#A3AED0]">
        <i class="fa fa-inbox" style="font-size:2rem; opacity:0.3; display:block; margin-bottom:8px;"></i>
        <p class="text-sm">Tidak ada data santri.</p>
    </div>
    @else
    @foreach($grouped as $kepkamNis => $santriList)
    @php $kp = $kepkams->get($kepkamNis); @endphp
    <div class="mb-6">
        {{-- Header Kepala Kamar --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background:#4318FF1A;">
                    <i class="fa fa-house text-xs" style="color:#4318FF;"></i>
                </div>
                <div>
                    @if($kp)
                        <h3 class="font-bold text-sm" style="color:#1B2559;">{{ $kp->nama }}</h3>
                        <p class="text-xs" style="color:#A3AED0;">{{ $kp->jabatan?->divisi?->nama ?? '-' }}</p>
                    @else
                        <h3 class="font-bold text-sm text-orange-500">Belum ada Kepala Kamar</h3>
                        <p class="text-xs" style="color:#A3AED0;">Santri belum diassign ke kepala kamar</p>
                    @endif
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:#F4F7FE; color:#4318FF;">
                {{ $santriList->count() }} santri
            </span>
        </div>

        {{-- Tabel Santri --}}
        <div class="card p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100" style="background:#F4F7FE80;">
                        <th class="text-left px-3 py-2.5 text-xs font-semibold uppercase w-8" style="color:#A3AED0;">#</th>
                        <th class="text-left px-3 py-2.5 text-xs font-semibold uppercase" style="color:#A3AED0;">Nama</th>
                        <th class="text-left px-3 py-2.5 text-xs font-semibold uppercase hidden sm:table-cell" style="color:#A3AED0;">NIS</th>
                        <th class="px-3 py-2.5 w-16"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($santriList as $i => $s)
                    <tr class="border-b border-gray-50 last:border-0" style="transition:background .15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
                        <td class="px-3 py-2.5 text-xs" style="color:#A3AED0;">{{ $i + 1 }}</td>
                        <td class="px-3 py-2.5 text-sm" style="color:#2B3674;">
                            <div class="font-medium">{{ $s->nama }}</div>
                            <div class="text-xs sm:hidden font-mono mt-0.5" style="color:#A3AED0;">{{ $s->nis }}</div>
                        </td>
                        <td class="px-3 py-2.5 font-mono text-xs hidden sm:table-cell" style="color:#A3AED0;">{{ $s->nis }}</td>
                        <td class="px-3 py-2.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="openEditSantri('{{ $s->nis }}', '{{ addslashes($s->nama) }}', '{{ $s->kepkam }}')"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-all"
                                    style="background:#F4F7FE; color:#4318FF;"
                                    onmouseover="this.style.background='#4318FF'; this.style.color='white'"
                                    onmouseout="this.style.background='#F4F7FE'; this.style.color='#4318FF'"
                                    title="Edit">
                                    <i class="fa fa-pen" style="font-size:9px;"></i>
                                </button>
                                <form method="POST" action="/mahadiyah/santri/{{ $s->nis }}"
                                    onsubmit="return confirm('Hapus santri {{ addslashes($s->nama) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center transition-all"
                                        style="background:#F4F7FE; color:#EE5D50;"
                                        onmouseover="this.style.background='#EE5D50'; this.style.color='white'"
                                        onmouseout="this.style.background='#F4F7FE'; this.style.color='#EE5D50'"
                                        title="Hapus">
                                        <i class="fa fa-trash" style="font-size:9px;"></i>
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

</div>

{{-- Modal Import CSV --}}
<div id="modal-import" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalImport()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold mb-1" style="color:#1B2559;">Import Santri dari CSV</h3>
        <p class="text-xs mb-4" style="color:#A3AED0;">Format: <span class="font-mono">nis, nama, kepkam</span> — baris pertama header.</p>
        <form method="POST" action="/mahadiyah/santri/import" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">Pilih File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" required
                    class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#4318FF]/10 file:text-[#4318FF] hover:file:bg-[#4318FF]/20 transition-all">
                <p class="text-xs mt-1.5" style="color:#A3AED0;">Maks. 2MB. Kolom <span class="font-mono">kepkam</span> boleh kosong.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1 rounded-xl">Import</button>
                <button type="button" onclick="closeModalImport()" class="flex-1 btn bg-white border border-gray-200 rounded-xl text-sm" style="color:#2B3674;">Batal</button>
            </div>
        </form>
        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
            <a href="/mahadiyah/santri/template" class="text-xs hover:underline" style="color:#4318FF;">
                <i class="fa fa-download mr-1"></i> Download template CSV
            </a>
        </div>
    </div>
</div>

{{-- Modal Tambah Santri --}}
<div id="modal-tambah" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalTambah()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold mb-4" style="color:#1B2559;">Tambah Santri</h3>
        <form method="POST" action="/mahadiyah/santri">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">NIS</label>
                <input type="text" name="nis" required placeholder="Nomor Induk Santri" class="form-control">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">Nama</label>
                <input type="text" name="nama" required placeholder="Nama lengkap santri" class="form-control">
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">Kepala Kamar</label>
                <select name="kepkam" class="form-control">
                    <option value="">— Belum ada —</option>
                    @foreach($kepkams as $k)
                    <option value="{{ $k->nis }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1 rounded-xl">Simpan</button>
                <button type="button" onclick="closeModalTambah()" class="flex-1 btn bg-white border border-gray-200 rounded-xl text-sm" style="color:#2B3674;">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Santri --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalEdit()"></div>
    <div class="relative bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.1)] w-full max-w-sm z-10 p-6">
        <h3 class="text-lg font-bold mb-4" style="color:#1B2559;">Edit Santri</h3>
        <form method="POST" id="form-edit-santri" action="">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">Nama</label>
                <input type="text" name="nama" id="edit-nama" required class="form-control">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">NIS</label>
                <input type="text" id="edit-nis-display" class="form-control" style="background:#F4F7FE; color:#A3AED0;" disabled>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold mb-1.5" style="color:#1B2559;">Kepala Kamar</label>
                <select name="kepkam" id="edit-kepkam" class="form-control">
                    <option value="">— Belum ada —</option>
                    @foreach($kepkams as $k)
                    <option value="{{ $k->nis }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1 rounded-xl">Simpan</button>
                <button type="button" onclick="closeModalEdit()" class="flex-1 btn bg-white border border-gray-200 rounded-xl text-sm" style="color:#2B3674;">Batal</button>
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
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:#EFF6FF;">
                    <i class="fa fa-circle-info" style="color:#3B82F6;"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold" style="color:#1B2559;">Tutorial Import CSV</h3>
                    <p class="text-xs" style="color:#A3AED0;">Cara edit file CSV dengan Excel lalu import ke sistem</p>
                </div>
            </div>
            <button onclick="closeModalTutorial()" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="space-y-3">
            @foreach([
                ['1','Download Template','Klik tombol <strong style="color:#2B3674">Template CSV</strong> untuk download file template.'],
                ['2','Buka di Excel','Buka Excel → <strong style="color:#2B3674">File → Open</strong> → <strong style="color:#2B3674">Blok Kolom A</strong> → pilih Menu <strong style="color:#2B3674">Data → Text to Columns</strong>, pilih <strong style="color:#2B3674">Delimited → Comma → Next sampai Finish</strong>.'],
                ['3','Isi Data Santri','Kolom <code style="background:#F4F7FE;padding:0 4px;border-radius:4px;color:#4318FF;">nis</code> = NIS santri, <code style="background:#F4F7FE;padding:0 4px;border-radius:4px;color:#4318FF;">nama</code> = nama lengkap, <code style="background:#F4F7FE;padding:0 4px;border-radius:4px;color:#4318FF;">kepkam</code> = NIS kepala kamar (boleh kosong).'],
                ['4','Save as CSV','<strong style="color:#2B3674">File → Save As</strong> → pilih format <strong style="color:#2B3674">CSV (Comma delimited)</strong> → Save → Yes.'],
                ['5','Import ke Sistem','Klik <strong style="color:#2B3674">Import CSV</strong>, pilih file CSV yang sudah diisi, lalu klik Import.'],
            ] as [$no, $judul, $desc])
            <div class="flex gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5" style="background:#4318FF;">{{ $no }}</span>
                <div>
                    <p class="text-sm font-semibold" style="color:#1B2559;">{{ $judul }}</p>
                    <p class="text-xs mt-0.5" style="color:#A3AED0;">{!! $desc !!}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center">
            <a href="/mahadiyah/santri/template" class="text-xs hover:underline flex items-center gap-1" style="color:#4318FF;">
                <i class="fa fa-download"></i> Download template CSV
            </a>
            <button onclick="closeModalTutorial()" class="btn btn-dark py-2 px-5 text-sm rounded-xl">Tutup</button>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function openModalImport()  { document.getElementById('modal-import').classList.remove('hidden'); document.getElementById('modal-import').classList.add('flex'); }
function closeModalImport() { document.getElementById('modal-import').classList.add('hidden'); document.getElementById('modal-import').classList.remove('flex'); }
function openModalTambah()  { document.getElementById('modal-tambah').classList.remove('hidden'); document.getElementById('modal-tambah').classList.add('flex'); }
function closeModalTambah() { document.getElementById('modal-tambah').classList.add('hidden'); document.getElementById('modal-tambah').classList.remove('flex'); }
function openModalTutorial()  { document.getElementById('modal-tutorial').classList.remove('hidden'); document.getElementById('modal-tutorial').classList.add('flex'); }
function closeModalTutorial() { document.getElementById('modal-tutorial').classList.add('hidden'); document.getElementById('modal-tutorial').classList.remove('flex'); }
function openEditSantri(nis, nama, kepkam) {
    document.getElementById('form-edit-santri').action = '/mahadiyah/santri/' + nis;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-nis-display').value = nis;
    document.getElementById('edit-kepkam').value = kepkam || '';
    document.getElementById('modal-edit').classList.remove('hidden');
    document.getElementById('modal-edit').classList.add('flex');
}
function closeModalEdit() { document.getElementById('modal-edit').classList.add('hidden'); document.getElementById('modal-edit').classList.remove('flex'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModalImport(); closeModalTambah(); closeModalEdit(); closeModalTutorial(); } });
</script>
@endsection
