@extends('mahadiyah.layout')

@section('content')
<div class="mt-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#1B2559]">Data Pengurus</h2>
            <p class="text-sm text-[#A3AED0] mt-1">Kelola pengurus, divisi, dan jabatan</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 bg-white rounded-2xl p-1.5 shadow-sm w-fit">
        <button onclick="switchTab('tab-pengurus')" id="btn-tab-pengurus"
            class="tab-btn active px-5 py-2 rounded-xl text-sm font-semibold transition-all">
            <i class="fa fa-users mr-2"></i>Pengurus
        </button>
        <button onclick="switchTab('tab-jabatan')" id="btn-tab-jabatan"
            class="tab-btn px-5 py-2 rounded-xl text-sm font-semibold transition-all">
            <i class="fa fa-id-badge mr-2"></i>Jabatan
        </button>
        <button onclick="switchTab('tab-divisi')" id="btn-tab-divisi"
            class="tab-btn px-5 py-2 rounded-xl text-sm font-semibold transition-all">
            <i class="fa fa-sitemap mr-2"></i>Divisi
        </button>
    </div>

    {{-- ===================== TAB PENGURUS ===================== --}}
    <div id="tab-pengurus">
        <div class="flex justify-end mb-4">
            <button onclick="openModal('modal-tambah-pengurus')"
                class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
                <i class="fa fa-plus mr-2"></i>Tambah Pengurus
            </button>
        </div>

        {{-- Kepala Kamar --}}
        <div class="mb-6">
            <h3 class="text-base font-bold text-[#1B2559] mb-3 flex items-center gap-2">
                <span class="w-2 h-5 bg-[#4318FF] rounded-full inline-block"></span>
                Kepala Kamar
            </h3>
            @php
                $pengurusKepkam = \App\Models\Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe','kepkam'))
                    ->with('jabatan')
                    ->orderBy('nama')
                    ->get();
            @endphp
            @if($pengurusKepkam->count())
            <div class="card">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($pengurusKepkam as $p)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#F4F7FE] hover:bg-[#EEF2FF] transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <i class="fa fa-user text-[#4318FF] text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-[#1B2559] text-sm truncate">{{ $p->nama }}</p>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-[#4318FF]/10 text-[#4318FF] text-xs font-medium">
                                    {{ $p->jabatan->nama ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1.5 flex-shrink-0 ml-2">
                            <button onclick="openEditPengurus('{{ $p->nis }}','{{ addslashes($p->nama) }}',{{ $p->jabatan_id ?? 'null' }})"
                                class="w-7 h-7 rounded-lg bg-white hover:bg-[#4318FF] text-[#4318FF] hover:text-white transition-all flex items-center justify-center shadow-sm">
                                <i class="fa fa-pen text-xs"></i>
                            </button>
                            <button onclick="confirmDeletePengurus('{{ $p->nis }}','{{ addslashes($p->nama) }}')"
                                class="w-7 h-7 rounded-lg bg-white hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white transition-all flex items-center justify-center shadow-sm">
                                <i class="fa fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="card text-center py-8 text-[#A3AED0]">
                <i class="fa fa-inbox text-3xl mb-2 block opacity-30"></i>
                <p class="text-sm">Belum ada Kepala Kamar. Tambahkan pengurus dengan jabatan bertipe Kepala Kamar.</p>
            </div>
            @endif
        </div>

        {{-- Non Kepala Kamar --}}
        <div>
            <h3 class="text-base font-bold text-[#1B2559] mb-3 flex items-center gap-2">
                <span class="w-2 h-5 bg-[#05CD99] rounded-full inline-block"></span>
                Non Kepala Kamar
            </h3>
            @forelse($divisiNon as $divisi)
            <div class="card mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-[#1B2559] text-sm">{{ $divisi->nama }}</h4>
                    <span class="text-xs text-[#A3AED0]">{{ $divisi->jabatan->sum(fn($j) => $j->pengurus->count()) }} orang</span>
                </div>
                @foreach($divisi->jabatan as $jab)
                    @if($jab->pengurus->count())
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-[#A3AED0] uppercase tracking-wide mb-2">{{ $jab->nama }}</p>
                        <div class="space-y-2">
                            @foreach($jab->pengurus as $p)
                            @include('mahadiyah._pengurus-row', ['p' => $p, 'jabNama' => $jab->nama, 'divNama' => $divisi->nama])
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @empty
            <div class="card text-center py-8 text-[#A3AED0]">
                <i class="fa fa-inbox text-3xl mb-2 block opacity-30"></i>
                <p class="text-sm">Belum ada divisi Non Kepala Kamar. Tambahkan di tab Divisi.</p>
            </div>
            @endforelse
        </div>

        {{-- Pengurus tanpa jabatan --}}
        @php
            $tanpaJabatan = \App\Models\Pengurus::whereNull('jabatan_id')->orderBy('nama')->get();
        @endphp
        @if($tanpaJabatan->count())
        <div class="mt-4">
            <h3 class="text-base font-bold text-[#1B2559] mb-3 flex items-center gap-2">
                <span class="w-2 h-5 bg-[#FFB547] rounded-full inline-block"></span>
                Belum Ada Jabatan
            </h3>
            <div class="card">
                <div class="space-y-2">
                    @foreach($tanpaJabatan as $p)
                    @include('mahadiyah._pengurus-row', ['p' => $p, 'jabNama' => null, 'divNama' => null])
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>{{-- end tab-pengurus --}}

    {{-- ===================== TAB JABATAN ===================== --}}
    <div id="tab-jabatan" class="hidden">
        <div class="flex justify-end mb-4">
            <button onclick="openModal('modal-tambah-jabatan')"
                class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
                <i class="fa fa-plus mr-2"></i>Tambah Jabatan
            </button>
        </div>
        <div class="card">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-[#A3AED0] border-b border-gray-100">
                        <th class="pb-3 font-semibold w-8">#</th>
                        <th class="pb-3 font-semibold">Nama Jabatan</th>
                        <th class="pb-3 font-semibold">Divisi / Bagian</th>
                        <th class="pb-3 font-semibold">Tipe</th>
                        <th class="pb-3 font-semibold">Jumlah Pengurus</th>
                        <th class="pb-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $allJabatanList = \App\Models\Jabatan::with('divisi')->withCount('pengurus')->orderBy('divisi_id')->orderBy('nama')->get(); @endphp
                    @forelse($allJabatanList as $i => $jab)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-3 text-[#A3AED0]">{{ $i+1 }}</td>
                        <td class="py-3 font-semibold text-[#1B2559]">{{ $jab->nama }}</td>
                        <td class="py-3 text-[#A3AED0]">
                            @if($jab->divisi?->tipe === 'kepkam')
                                <span class="text-xs italic text-gray-300">—</span>
                            @else
                                {{ $jab->divisi->nama ?? '-' }}
                            @endif
                        </td>
                        <td class="py-3">
                            @if($jab->divisi?->tipe === 'kepkam')
                                <span class="px-2 py-0.5 rounded-lg bg-[#4318FF]/10 text-[#4318FF] text-xs font-semibold">Kepala Kamar</span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg bg-[#05CD99]/10 text-[#05CD99] text-xs font-semibold">Non Kepkam</span>
                            @endif
                        </td>
                        <td class="py-3 text-[#A3AED0]">{{ $jab->pengurus_count }} orang</td>
                        <td class="py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditJabatan({{ $jab->id }},'{{ addslashes($jab->nama) }}',{{ $jab->divisi_id }})"
                                    class="w-8 h-8 rounded-lg bg-[#4318FF]/10 hover:bg-[#4318FF] text-[#4318FF] hover:text-white transition-all flex items-center justify-center">
                                    <i class="fa fa-pen text-xs"></i>
                                </button>
                                <button onclick="confirmDeleteJabatan({{ $jab->id }},'{{ addslashes($jab->nama) }}')"
                                    class="w-8 h-8 rounded-lg bg-[#EE5D50]/10 hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white transition-all flex items-center justify-center">
                                    <i class="fa fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-12 text-center text-[#A3AED0] text-sm">Belum ada jabatan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>{{-- end tab-jabatan --}}

    {{-- ===================== TAB DIVISI ===================== --}}
    <div id="tab-divisi" class="hidden">
        <div class="flex justify-end mb-4">
            <button onclick="openModal('modal-tambah-divisi')"
                class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
                <i class="fa fa-plus mr-2"></i>Tambah Divisi
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card">
                <h4 class="font-bold text-[#1B2559] mb-1 flex items-center gap-2">
                    <span class="w-2 h-4 bg-[#4318FF] rounded-full"></span>Kepala Kamar
                </h4>
                <p class="text-xs text-[#A3AED0] mb-3">Jabatan kepkam dikelola langsung di tab Jabatan, tidak perlu divisi.</p>
                <div class="p-4 rounded-xl bg-[#F4F7FE] text-center">
                    @php $jabKepkam = \App\Models\Jabatan::whereHas('divisi', fn($q) => $q->where('tipe','kepkam'))->count(); @endphp
                    <p class="text-2xl font-bold text-[#4318FF]">{{ $jabKepkam }}</p>
                    <p class="text-xs text-[#A3AED0] mt-1">Jabatan Kepala Kamar terdaftar</p>
                </div>
            </div>
            <div class="card">
                <h4 class="font-bold text-[#1B2559] mb-3 flex items-center gap-2">
                    <span class="w-2 h-4 bg-[#05CD99] rounded-full"></span>Non Kepala Kamar
                </h4>
                <div class="space-y-2">
                    @forelse($divisiNon as $d)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[#F4F7FE]">
                        <span class="font-medium text-[#1B2559] text-sm">{{ $d->nama }}</span>
                        <div class="flex gap-2">
                            <button onclick="openEditDivisi({{ $d->id }},'{{ addslashes($d->nama) }}','{{ $d->tipe }}')"
                                class="w-7 h-7 rounded-lg bg-[#4318FF]/10 hover:bg-[#4318FF] text-[#4318FF] hover:text-white transition-all flex items-center justify-center">
                                <i class="fa fa-pen text-xs"></i>
                            </button>
                            <button onclick="confirmDeleteDivisi({{ $d->id }},'{{ addslashes($d->nama) }}')"
                                class="w-7 h-7 rounded-lg bg-[#EE5D50]/10 hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white transition-all flex items-center justify-center">
                                <i class="fa fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-[#A3AED0] italic text-center py-4">Belum ada divisi</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>{{-- end tab-divisi --}}

</div>{{-- end main container --}}

{{-- ============================================================ --}}
{{-- MODALS                                                        --}}
{{-- ============================================================ --}}

{{-- Modal Tambah Pengurus --}}
<div id="modal-tambah-pengurus" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-tambah-pengurus')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#1B2559]">Tambah Pengurus</h3>
            <button onclick="closeModal('modal-tambah-pengurus')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500"><i class="fa fa-times text-sm"></i></button>
        </div>
        <form action="/mahadiyah/pengurus" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">NIS <span class="text-red-400">*</span></label>
                <input type="text" name="nis" required placeholder="Contoh: 112441223"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Ust. Ahmad Fauzi"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Jabatan</label>
                <select name="jabatan_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                    <option value="">-- Pilih Jabatan --</option>
                    @php
                        $jabatanKepkam = $allJabatan->filter(fn($j) => $j->divisi?->tipe === 'kepkam');
                        $jabatanNon    = $allJabatan->filter(fn($j) => $j->divisi?->tipe !== 'kepkam');
                    @endphp
                    @if($jabatanKepkam->count())
                    <optgroup label="── Kepala Kamar ──">
                        @foreach($jabatanKepkam as $jab)
                        <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </optgroup>
                    @endif
                    @if($jabatanNon->count())
                    @foreach($jabatanNon->groupBy(fn($j) => $j->divisi->nama ?? 'Lainnya') as $divNama => $jabList)
                    <optgroup label="{{ $divNama }}">
                        @foreach($jabList as $jab)
                        <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                    @endif
                </select>
                <p class="text-[10px] text-[#A3AED0] mt-1">Untuk Kepala Kamar: pilih opsi di grup "Kepala Kamar" — contoh: <em>Kepala Kamar 1 SMA</em>, <em>Kepala Kamar 2 SMP</em></p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-tambah-pengurus')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold shadow-lg shadow-blue-500/30 text-sm"><i class="fa fa-save mr-1.5"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Pengurus --}}
<div id="modal-edit-pengurus" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-edit-pengurus')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#1B2559]">Edit Pengurus</h3>
            <button onclick="closeModal('modal-edit-pengurus')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500"><i class="fa fa-times text-sm"></i></button>
        </div>
        <form id="form-edit-pengurus" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">NIS</label>
                <input type="text" id="ep-nis" disabled class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-gray-400 text-sm cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="nama" id="ep-nama" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Jabatan</label>
                <select name="jabatan_id" id="ep-jabatan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                    <option value="">-- Tanpa Jabatan --</option>
                    @php
                        $jabatanKepkam = $allJabatan->filter(fn($j) => $j->divisi?->tipe === 'kepkam');
                        $jabatanNon    = $allJabatan->filter(fn($j) => $j->divisi?->tipe !== 'kepkam');
                    @endphp
                    @if($jabatanKepkam->count())
                    <optgroup label="── Kepala Kamar ──">
                        @foreach($jabatanKepkam as $jab)
                        <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </optgroup>
                    @endif
                    @if($jabatanNon->count())
                    @foreach($jabatanNon->groupBy(fn($j) => $j->divisi->nama ?? 'Lainnya') as $divNama => $jabList)
                    <optgroup label="{{ $divNama }}">
                        @foreach($jabList as $jab)
                        <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                    @endif
                </select>
                <p class="text-[10px] text-[#A3AED0] mt-1">Untuk Kepala Kamar: pilih opsi di grup "Kepala Kamar" — contoh: <em>Kepala Kamar 1 SMA</em>, <em>Kepala Kamar 2 SMP</em></p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-pengurus')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold shadow-lg shadow-blue-500/30 text-sm"><i class="fa fa-save mr-1.5"></i>Perbarui</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Hapus Pengurus --}}
<div id="modal-hapus-pengurus" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-hapus-pengurus')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 animate-modal">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-[#EE5D50]/10 flex items-center justify-center mx-auto mb-4"><i class="fa fa-trash text-[#EE5D50] text-xl"></i></div>
            <h3 class="text-lg font-bold text-[#1B2559] mb-1">Hapus Pengurus?</h3>
            <p class="text-sm text-[#A3AED0] mb-5">Data <strong id="hp-nama" class="text-[#1B2559]"></strong> akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-hapus-pengurus')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <form id="form-hapus-pengurus" action="" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-[#EE5D50] hover:bg-[#D43F33] text-white font-semibold text-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Divisi --}}
<div id="modal-tambah-divisi" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-tambah-divisi')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#1B2559]">Tambah Divisi</h3>
            <button onclick="closeModal('modal-tambah-divisi')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500"><i class="fa fa-times text-sm"></i></button>
        </div>
        <form action="/mahadiyah/divisi" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama Divisi <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Multimedia"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Tipe <span class="text-red-400">*</span></label>
                <select name="tipe" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                    <option value="kepkam">Kepala Kamar</option>
                    <option value="non">Non Kepala Kamar</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-tambah-divisi')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold shadow-lg shadow-blue-500/30 text-sm"><i class="fa fa-save mr-1.5"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Divisi --}}
<div id="modal-edit-divisi" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-edit-divisi')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#1B2559]">Edit Divisi</h3>
            <button onclick="closeModal('modal-edit-divisi')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500"><i class="fa fa-times text-sm"></i></button>
        </div>
        <form id="form-edit-divisi" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama Divisi <span class="text-red-400">*</span></label>
                <input type="text" name="nama" id="ed-nama" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Tipe <span class="text-red-400">*</span></label>
                <select name="tipe" id="ed-tipe" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                    <option value="kepkam">Kepala Kamar</option>
                    <option value="non">Non Kepala Kamar</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-divisi')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold shadow-lg shadow-blue-500/30 text-sm"><i class="fa fa-save mr-1.5"></i>Perbarui</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Hapus Divisi --}}
<div id="modal-hapus-divisi" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-hapus-divisi')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 animate-modal">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-[#EE5D50]/10 flex items-center justify-center mx-auto mb-4"><i class="fa fa-trash text-[#EE5D50] text-xl"></i></div>
            <h3 class="text-lg font-bold text-[#1B2559] mb-1">Hapus Divisi?</h3>
            <p class="text-sm text-[#A3AED0] mb-5">Divisi <strong id="hd-nama" class="text-[#1B2559]"></strong> dan semua jabatan di dalamnya akan dihapus.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-hapus-divisi')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <form id="form-hapus-divisi" action="" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-[#EE5D50] hover:bg-[#D43F33] text-white font-semibold text-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Jabatan --}}
<div id="modal-tambah-jabatan" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-tambah-jabatan')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-[#1B2559]">Tambah Jabatan</h3>
                <p class="text-xs text-[#A3AED0] mt-0.5">Pilih divisi, lalu isi semua jabatan sekaligus</p>
            </div>
            <button onclick="closeModal('modal-tambah-jabatan')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500"><i class="fa fa-times text-sm"></i></button>
        </div>
        <form action="/mahadiyah/jabatan" method="POST" class="p-6">
            @csrf
            {{-- Pilih Divisi --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Divisi <span class="text-red-400">*</span></label>
                <select name="divisi_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                    <option value="">-- Pilih Divisi --</option>
                    <optgroup label="── Kepala Kamar ──">
                        @foreach((\App\Models\Divisi::where('tipe','kepkam')->orderBy('nama')->get()) as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="── Non Kepala Kamar ──">
                        @foreach((\App\Models\Divisi::where('tipe','non')->orderBy('nama')->get()) as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <p class="text-xs text-[#A3AED0] mt-1">Untuk Kepkam isi misal: <em>Kepala Kamar 1 SMP</em>, <em>Kepala Kamar 2 SMP</em></p>
            </div>

            {{-- Daftar Jabatan --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-[#1B2559]">Nama Jabatan <span class="text-red-400">*</span></label>
                    <button type="button" onclick="tambahBaris()"
                        class="text-xs text-[#4318FF] hover:text-[#3311CC] font-semibold flex items-center gap-1 transition-colors">
                        <i class="fa fa-plus text-xs"></i> Tambah Baris
                    </button>
                </div>

                <div id="jabatan-list" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    {{-- Baris awal --}}
                    <div class="jabatan-baris flex items-center gap-2">
                        <input type="text" name="nama[]" placeholder="Contoh: Ketua" required
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                        <button type="button" onclick="hapusBaris(this)" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-[#EE5D50] text-gray-400 hover:text-white transition-all flex items-center justify-center flex-shrink-0 opacity-0 pointer-events-none">
                            <i class="fa fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-tambah-jabatan')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold shadow-lg shadow-blue-500/30 text-sm"><i class="fa fa-save mr-1.5"></i>Simpan Semua</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Jabatan --}}
<div id="modal-edit-jabatan" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-edit-jabatan')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-[#1B2559]">Edit Jabatan</h3>
            <button onclick="closeModal('modal-edit-jabatan')" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500"><i class="fa fa-times text-sm"></i></button>
        </div>
        <form id="form-edit-jabatan" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Divisi <span class="text-red-400">*</span></label>
                <select name="divisi_id" id="ej-divisi" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                    @foreach((\App\Models\Divisi::orderBy('tipe')->orderBy('nama')->get()) as $d)
                    <option value="{{ $d->id }}">[{{ $d->tipe === 'kepkam' ? 'Kepkam' : 'Non' }}] {{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Nama Jabatan <span class="text-red-400">*</span></label>
                <input type="text" name="nama" id="ej-nama" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-jabatan')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold shadow-lg shadow-blue-500/30 text-sm"><i class="fa fa-save mr-1.5"></i>Perbarui</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Hapus Jabatan --}}
<div id="modal-hapus-jabatan" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-hapus-jabatan')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 animate-modal">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-[#EE5D50]/10 flex items-center justify-center mx-auto mb-4"><i class="fa fa-trash text-[#EE5D50] text-xl"></i></div>
            <h3 class="text-lg font-bold text-[#1B2559] mb-1">Hapus Jabatan?</h3>
            <p class="text-sm text-[#A3AED0] mb-5">Jabatan <strong id="hj-nama" class="text-[#1B2559]"></strong> akan dihapus. Pengurus yang memiliki jabatan ini akan menjadi tanpa jabatan.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-hapus-jabatan')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 text-sm">Batal</button>
                <form id="form-hapus-jabatan" action="" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-[#EE5D50] hover:bg-[#D43F33] text-white font-semibold text-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<style>
    .animate-modal { animation: modalIn 0.2s ease-out; }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(8px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .tab-btn { color: #A3AED0; }
    .tab-btn.active { background: #4318FF; color: white; box-shadow: 0 4px 15px rgba(67,24,255,0.3); }
</style>
<script>
    // ---- Tab Switching ----
    function switchTab(tabId) {
        ['tab-pengurus','tab-jabatan','tab-divisi'].forEach(id => {
            document.getElementById(id).classList.add('hidden');
            document.getElementById('btn-' + id).classList.remove('active');
        });
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById('btn-' + tabId).classList.add('active');
    }

    // ---- Modal Helpers ----
    function openModal(id) {
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        el.classList.add('flex');
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.classList.remove('flex');
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(m => {
                m.classList.add('hidden'); m.classList.remove('flex');
            });
        }
    });

    // ---- Pengurus ----
    function openEditPengurus(nis, nama, jabatanId) {
        document.getElementById('ep-nis').value = nis;
        document.getElementById('ep-nama').value = nama;
        document.getElementById('ep-jabatan').value = jabatanId || '';
        document.getElementById('form-edit-pengurus').action = '/mahadiyah/pengurus/' + nis;
        openModal('modal-edit-pengurus');
    }
    function confirmDeletePengurus(nis, nama) {
        document.getElementById('hp-nama').textContent = nama;
        document.getElementById('form-hapus-pengurus').action = '/mahadiyah/pengurus/' + nis;
        openModal('modal-hapus-pengurus');
    }

    // ---- Divisi ----
    function openEditDivisi(id, nama, tipe) {
        document.getElementById('ed-nama').value = nama;
        document.getElementById('ed-tipe').value = tipe;
        document.getElementById('form-edit-divisi').action = '/mahadiyah/divisi/' + id;
        openModal('modal-edit-divisi');
    }
    function confirmDeleteDivisi(id, nama) {
        document.getElementById('hd-nama').textContent = nama;
        document.getElementById('form-hapus-divisi').action = '/mahadiyah/divisi/' + id;
        openModal('modal-hapus-divisi');
    }

    // ---- Jabatan ----
    function openEditJabatan(id, nama, divisiId) {
        document.getElementById('ej-nama').value = nama;
        document.getElementById('ej-divisi').value = divisiId;
        document.getElementById('form-edit-jabatan').action = '/mahadiyah/jabatan/' + id;
        openModal('modal-edit-jabatan');
    }
    function confirmDeleteJabatan(id, nama) {
        document.getElementById('hj-nama').textContent = nama;
        document.getElementById('form-hapus-jabatan').action = '/mahadiyah/jabatan/' + id;
        openModal('modal-hapus-jabatan');
    }

    // ---- Tambah Jabatan Bulk ----
    function tambahBaris() {
        const list = document.getElementById('jabatan-list');
        const baris = document.createElement('div');
        baris.className = 'jabatan-baris flex items-center gap-2';
        baris.innerHTML = `
            <input type="text" name="nama[]" placeholder="Nama jabatan..."
                class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none"
                autofocus>
            <button type="button" onclick="hapusBaris(this)"
                class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-[#EE5D50] text-gray-400 hover:text-white transition-all flex items-center justify-center flex-shrink-0">
                <i class="fa fa-times text-xs"></i>
            </button>
        `;
        list.appendChild(baris);
        updateHapusButtons();
        // Focus input baru
        baris.querySelector('input').focus();
    }

    function hapusBaris(btn) {
        const baris = btn.closest('.jabatan-baris');
        baris.remove();
        updateHapusButtons();
    }

    function updateHapusButtons() {
        const semua = document.querySelectorAll('.jabatan-baris');
        semua.forEach((baris, i) => {
            const btn = baris.querySelector('button');
            if (semua.length === 1) {
                // Hanya 1 baris, sembunyikan tombol hapus
                btn.classList.add('opacity-0', 'pointer-events-none');
            } else {
                btn.classList.remove('opacity-0', 'pointer-events-none');
            }
        });
    }

    // Reset modal jabatan saat ditutup
    document.getElementById('modal-tambah-jabatan').addEventListener('click', function(e) {
        if (e.target === this || e.target.closest('[onclick="closeModal(\'modal-tambah-jabatan\')"]')) {
            resetJabatanModal();
        }
    });

    function resetJabatanModal() {
        const list = document.getElementById('jabatan-list');
        // Sisakan hanya 1 baris
        const baris = list.querySelectorAll('.jabatan-baris');
        baris.forEach((b, i) => { if (i > 0) b.remove(); });
        // Kosongkan input pertama
        const firstInput = list.querySelector('input');
        if (firstInput) firstInput.value = '';
        updateHapusButtons();
    }

    // Enter key untuk tambah baris baru
    document.getElementById('jabatan-list').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            tambahBaris();
        }
    });
</script>
@endsection
