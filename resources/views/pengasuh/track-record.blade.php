@extends('pengasuh.layout')

@section('title', 'Track Record Santri')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Profil & Track Record</h1>
@endsection

@section('content')
    <!-- Search -->
    <div class="relative z-20 mb-6">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchSantri" placeholder="Cari santri berdasarkan nama atau NIS..."
                    class="w-full bg-white border border-gray-100 text-gray-600 text-sm rounded-xl h-12 pl-10 pr-4 focus:ring-2 focus:ring-[#4318FF] focus:outline-none shadow-sm">
            </div>
        </div>
        <!-- Dropdown Hasil Pencarian -->
        <div id="searchResults" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-xl max-h-60 overflow-y-auto shadow-xl z-30"></div>
    </div>

    <!-- Profile Section (Hidden initially) -->
    <div id="profileSection" class="hidden space-y-4 md:space-y-6">
        <!-- Identitas & Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            <!-- Kartu Identitas -->
            <div class="card md:col-span-1">
                <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                    <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-[#4318FF]/10 flex items-center justify-center text-[#4318FF] text-2xl md:text-3xl shrink-0">
                        <i class="fa fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-[#1B2559]" id="profNama">Nama Santri</h3>
                        <p class="text-[#A3AED0] text-xs md:text-sm" id="profNis">NIS: -</p>
                    </div>
                </div>
                <div class="pt-4 space-y-2">
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                        <span class="text-[10px] text-[#A3AED0]">Kamar</span>
                        <span class="text-xs font-bold text-[#1B2559]" id="profKamar">-</span>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                        <span class="text-[10px] text-[#A3AED0]">Asrama</span>
                        <span class="text-xs font-bold text-[#1B2559]" id="profAsrama">-</span>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                        <span class="text-xs text-[#A3AED0]">Kepala Kamar</span>
                        <span class="text-xs font-bold text-[#1B2559]" id="profKepalaKamar">-</span>
                    </div>
                </div>
            </div>

            <!-- Statistik Grid -->
            <div class="card md:col-span-2">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] mb-3">Ringkasan Track Record</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 mb-4">
                    <div class="bg-red-50 p-3 rounded-xl text-center border border-red-100">
                        <p class="text-xl md:text-2xl font-bold text-red-600" id="statPelanggaran">0</p>
                        <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-bold uppercase">Pelanggaran</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-xl text-center border border-green-100">
                        <p class="text-xl md:text-2xl font-bold text-green-600" id="statPrestasi">0</p>
                        <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-bold uppercase">Prestasi</p>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-xl text-center border border-yellow-100">
                        <p class="text-xl md:text-2xl font-bold text-yellow-600" id="statAlfa">0</p>
                        <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-bold uppercase">Total Alfa</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-xl text-center border border-blue-100">
                        <p class="text-xl md:text-2xl font-bold text-blue-600" id="statPoin">100</p>
                        <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-bold uppercase">Sisa Poin</p>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="flex justify-between items-end mb-1">
                        <h4 class="font-bold text-[10px] md:text-sm text-[#1B2559]">Kehadiran</h4>
                        <p class="text-[10px] md:text-xs font-bold text-green-500" id="txtKehadiran">0%</p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" id="barKehadiran" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Detail -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Pelanggaran -->
            <div class="card bg-red-50/30">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] mb-3 flex items-center justify-between">
                    Pelanggaran
                    <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded font-bold uppercase">Poin (-)</span>
                </h3>
                <div class="space-y-2" id="listPelanggaran"></div>
            </div>

            <!-- Prestasi -->
            <div class="card bg-green-50/30">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] mb-3 flex items-center justify-between">
                    Prestasi / Kebaikan
                    <span class="text-[10px] bg-green-100 text-green-600 px-2 py-0.5 rounded font-bold uppercase">Coming Soon</span>
                </h3>
                <div class="space-y-2" id="listPrestasi">
                    <div class="p-3 text-center text-xs text-[#A3AED0] bg-white rounded-xl border border-gray-100">Modul prestasi sedang dalam pengembangan.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="card text-center py-12 md:py-24 mt-4">
        <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-3xl md:text-4xl">
            <i class="fa fa-search"></i>
        </div>
        <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-1">Cari Profil Santri</h3>
        <p class="text-xs md:text-sm text-[#A3AED0]">Ketik nama atau NIS untuk melihat track record detail.</p>
    </div>
@endsection

@push('scripts')
<script>
const searchInput = document.getElementById('searchSantri');
const resultsDiv = document.getElementById('searchResults');
let debounceTimer;

searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const query = this.value.trim();
        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }
        fetch(`/pengasuh/api/search-santri?q=${encodeURIComponent(query)}`)
            .then(r => r.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(s => {
                        resultsDiv.innerHTML += `
                            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0 flex items-center gap-3" onclick="showProfile('${s.nis}')">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><i class="fa fa-user text-xs"></i></div>
                                <div>
                                    <p class="font-bold text-[#1B2559] text-xs md:text-sm">${s.nama}</p>
                                    <p class="text-[10px] text-[#A3AED0]">NIS: ${s.nis}</p>
                                </div>
                            </div>`;
                    });
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsDiv.innerHTML = '<div class="p-4 text-center text-xs text-[#A3AED0]">Santri tidak ditemukan</div>';
                    resultsDiv.classList.remove('hidden');
                }
            });
    }, 300);
});

function showProfile(nis) {
    resultsDiv.classList.add('hidden');
    searchInput.value = '';

    fetch(`/pengasuh/api/santri/${nis}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('profileSection').classList.remove('hidden');

            document.getElementById('profNama').textContent = data.nama;
            document.getElementById('profNis').textContent = `NIS: ${data.nis}`;
            document.getElementById('profKamar').textContent = data.kamar;
            document.getElementById('profAsrama').textContent = data.asrama;
            document.getElementById('profKepalaKamar').textContent = data.kepkam;

            document.getElementById('statPelanggaran').textContent = data.stats.pelanggaran;
            document.getElementById('statPrestasi').textContent = data.stats.prestasi;
            document.getElementById('statAlfa').textContent = data.stats.alfa;
            document.getElementById('statPoin').textContent = data.stats.poin;

            const bar = document.getElementById('barKehadiran');
            bar.style.width = `${data.stats.kehadiran}%`;
            bar.className = `h-2 rounded-full ${data.stats.kehadiran >= 90 ? 'bg-green-500' : data.stats.kehadiran >= 75 ? 'bg-yellow-500' : 'bg-red-500'}`;

            const txt = document.getElementById('txtKehadiran');
            txt.textContent = `${data.stats.kehadiran}%`;
            txt.className = `text-[10px] md:text-xs font-bold ${data.stats.kehadiran >= 90 ? 'text-green-500' : data.stats.kehadiran >= 75 ? 'text-yellow-500' : 'text-red-500'}`;

            // Pelanggaran list
            const listP = document.getElementById('listPelanggaran');
            listP.innerHTML = '';
            if (data.pelanggaran.length > 0) {
                data.pelanggaran.forEach(p => {
                    listP.innerHTML += `
                        <div class="flex items-start gap-3 p-2.5 bg-white rounded-xl border border-red-100 shadow-sm">
                            <div class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5"><i class="fa fa-times text-[10px]"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs md:text-sm font-bold text-[#1B2559]" style="white-space: normal;">${p.desc}</p>
                                <div class="flex justify-between items-end mt-1">
                                    <span class="text-[9px] md:text-[10px] text-[#A3AED0]">${p.hukuman || ''}</span>
                                    <span class="text-[10px] md:text-xs font-bold text-red-600">-${p.poin} Pts</span>
                                </div>
                            </div>
                        </div>`;
                });
            } else {
                listP.innerHTML = '<div class="p-3 text-center text-xs text-[#A3AED0] bg-white rounded-xl border border-gray-100">Tidak ada riwayat pelanggaran.</div>';
            }

            // Prestasi (Coming Soon)
            document.getElementById('listPrestasi').innerHTML = '<div class="p-3 text-center text-xs text-[#A3AED0] bg-white rounded-xl border border-gray-100">Modul prestasi sedang dalam pengembangan.</div>';
        });
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
        resultsDiv.classList.add('hidden');
    }
});
</script>
@endpush
