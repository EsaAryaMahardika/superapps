@extends('mahadiyah.layout')

@section('content')
<div class="mt-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#1B2559]">Edit Absensi {{ $judul }}</h2>
            <p class="text-sm text-[#A3AED0] mt-1">
                {{ $tanggalDisplay->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="/mahadiyah/absensi-pengurus"
                class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all text-sm">
                <i class="fa fa-times mr-2"></i>Batal
            </a>
            <button type="submit" form="formEditAbsensi"
                class="px-6 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
                <i class="fa fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </div>

    <form action="/mahadiyah/edit-absen/{{ $tipe }}/{{ $tanggal }}" method="POST" id="formEditAbsensi">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
                <div>
                    <h3 class="text-lg font-bold text-[#1B2559]">Daftar Pengurus</h3>
                    <p class="text-xs text-[#A3AED0] mt-0.5">
                        {{ $pengurus->count() }} pengurus tercatat
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <div class="relative flex-1 sm:flex-initial">
                        <input type="text" id="searchInput" placeholder="Cari nama..."
                            class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm outline-none">
                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="setAll('H')"
                            class="px-3 py-2 text-xs font-semibold rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors whitespace-nowrap">
                            <i class="fa fa-check mr-1"></i>Semua Hadir
                        </button>
                        <button type="button" onclick="setAll('A')"
                            class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors whitespace-nowrap">
                            <i class="fa fa-times mr-1"></i>Semua Alpa
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-[#A3AED0] border-b border-gray-100">
                            <th class="pb-3 font-semibold">Nama Pengurus</th>
                            <th class="pb-3 font-semibold hidden md:table-cell">Jabatan</th>
                            <th class="pb-3 font-semibold">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        @foreach($pengurus as $p)
                        @php
                            $jabatan = $p->jabatan?->nama ?? '';
                            $divisi  = $p->jabatan?->divisi?->nama ?? '';
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors pengurus-row">
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#F4F7FE] flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-user text-[#4318FF] text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[#1B2559] nama-col">{{ $p->nama }}</p>
                                        <div class="flex flex-col gap-0.5">
                                            <p class="text-xs text-gray-400 font-mono">NIS: {{ $p->nis }}</p>
                                            @if($jabatan)
                                                <p class="text-xs text-gray-500 md:hidden font-medium">
                                                    {{ $jabatan }} @if($divisi) · {{ $divisi }} @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 hidden md:table-cell">
                                @if($jabatan)
                                    <p class="text-sm text-[#1B2559]">{{ $jabatan }}</p>
                                    @if($divisi)
                                        <p class="text-xs text-[#4318FF]">{{ $divisi }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-300 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5">
                                @php
                                    $rec       = $existing[$p->nis] ?? null;
                                    $statusNow = $rec?->status ?? 'H';
                                @endphp
                                <select name="pengurus[{{ $p->nis }}]"
                                    class="status-select px-3 py-2 rounded-lg border font-medium text-sm min-w-[120px] md:min-w-[130px] outline-none transition-all">
                                    @foreach(['H'=>'Hadir','S'=>'Sakit','I'=>'Izin','A'=>'Alpa'] as $k=>$l)
                                    <option value="{{ $k }}" {{ $k === $statusNow ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    function setAll(status) {
        document.querySelectorAll('.status-select').forEach(s => {
            s.value = status;
            updateColor(s);
        });
    }

    function updateColor(select) {
        const colors = {
            'H': 'text-green-600 border-green-200 bg-green-50',
            'S': 'text-yellow-600 border-yellow-200 bg-yellow-50',
            'I': 'text-blue-600 border-blue-200 bg-blue-50',
            'A': 'text-red-600 border-red-200 bg-red-50',
        };
        select.className = 'status-select px-3 py-2 rounded-lg border font-medium text-sm min-w-[130px] outline-none transition-all';
        if (colors[select.value]) select.className += ' ' + colors[select.value];
    }

    document.querySelectorAll('.status-select').forEach(s => {
        updateColor(s);
        s.addEventListener('change', function() { updateColor(this); });
    });

    document.getElementById('searchInput').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.pengurus-row').forEach(row => {
            const nama = row.querySelector('.nama-col')?.textContent.toLowerCase() ?? '';
            row.style.display = nama.includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
