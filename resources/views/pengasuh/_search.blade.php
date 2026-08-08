{{-- Overlay pencarian global — dipakai dari navbar mana pun. --}}
<div id="searchOverlay" class="fixed inset-0 z-[60] hidden bg-black/40 backdrop-blur-sm" onclick="closeSearchOnBackdrop(event)">
    <div class="mx-auto mt-0 md:mt-20 w-full md:max-w-2xl bg-white md:rounded-2xl shadow-2xl overflow-hidden h-full md:h-auto flex flex-col">
        <!-- Input -->
        <div class="p-3 md:p-4 border-b border-gray-100 flex items-center gap-2 shrink-0">
            <div class="relative flex-1">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="globalSearch" autocomplete="off"
                    placeholder="Cari menu, laporan, atau nama/NIS santri & pengurus..."
                    class="w-full bg-[#F4F7FE] border-none text-gray-600 text-sm rounded-xl h-12 pl-10 pr-4 focus:ring-2 focus:ring-[#4318FF] focus:outline-none">
            </div>
            <button type="button" onclick="closeSearch()"
                class="px-3 h-12 text-xs font-bold text-[#A3AED0] hover:text-[#2B3674] transition-colors shrink-0">
                Tutup
            </button>
        </div>
        <!-- Hasil -->
        <div id="globalSearchResults" class="overflow-y-auto flex-1 md:max-h-96">
            <div class="p-6 text-center text-xs text-[#A3AED0]">Ketik minimal 2 huruf untuk mencari.</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Pencarian Global (menu & sub-fitur + santri + pengurus) ─────────
const MENU_ITEMS = [
    { label: 'Rekap Absensi Santri', group: 'Absensi', icon: 'fa-clipboard-list', href: '/pengasuh/absensi', keywords: 'absen absensi kehadiran santri rekap jamaah sholat ngaji waqiah' },
    { label: 'Santri Perhatian Khusus', group: 'Absensi', icon: 'fa-user-times', href: '/pengasuh/absensi', keywords: 'kritis alfa bolos perhatian khusus santri bermasalah' },
    { label: 'Perizinan Santri', group: 'Perizinan', icon: 'fa-hand-paper', href: '/pengasuh/perizinan', keywords: 'izin perizinan keluar pulang santri menunggu terlambat approve' },
    { label: 'Daftar Pembayaran', group: 'Keuangan', icon: 'fa-money-bill-wave', href: '/pengasuh/pembayaran', keywords: 'bayar pembayaran keuangan spp syahriah lunas cicilan menunggak tagihan' },
    { label: 'Top Tunggakan SPP', group: 'Keuangan', icon: 'fa-triangle-exclamation', href: '/pengasuh/pembayaran', keywords: 'tunggakan nunggak hutang spp telat bayar keuangan' },
    { label: 'IKS Poskestren', group: 'Kesehatan', icon: 'fa-heartbeat', href: '/pengasuh/kesehatan', keywords: 'kesehatan iks poskestren sakit dirawat rawat jalan dirujuk rumah sakit pasien' },
    { label: 'Progres Pembangunan', group: 'Infrastruktur', icon: 'fa-hard-hat', href: '/pengasuh/infrastruktur', keywords: 'infrastruktur pembangunan proyek renovasi asrama masjid progres' },
    { label: 'Laporan Perairan', group: 'Infrastruktur', icon: 'fa-tint', href: '/pengasuh/infrastruktur', keywords: 'perairan air pipa bocor keruh mati kamar mandi infrastruktur' },
    { label: 'Distribusi Galon', group: 'Logistik', icon: 'fa-bottle-water', href: '/pengasuh/logistik', keywords: 'logistik galon air minum distribusi kirim asrama' },
    { label: 'Operasional Laundry', group: 'Logistik', icon: 'fa-shirt', href: '/pengasuh/logistik', keywords: 'laundry cuci setrika mesin deterjen antrian logistik' },
    { label: 'Track Record Santri', group: 'Profil', icon: 'fa-user-graduate', href: '/pengasuh/track-record?type=santri', keywords: 'profil santri track record riwayat pelanggaran prestasi poin' },
    { label: 'Track Record Pengurus', group: 'Profil', icon: 'fa-user-tie', href: '/pengasuh/track-record?type=pengurus', keywords: 'profil pengurus track record riwayat bandongan wirid yasinan' },
    { label: 'Profil Saya', group: 'Akun', icon: 'fa-user-circle', href: '/profil', keywords: 'akun profil saya password sandi ubah' },
];

const gOverlay     = document.getElementById('searchOverlay');
const gSearchInput = document.getElementById('globalSearch');
const gResultsDiv  = document.getElementById('globalSearchResults');
let gDebounce;

function openSearch() {
    gOverlay.classList.remove('hidden');
    setTimeout(() => gSearchInput.focus(), 50);
}

function closeSearch() {
    gOverlay.classList.add('hidden');
    gSearchInput.value = '';
    gResultsDiv.innerHTML = '<div class="p-6 text-center text-xs text-[#A3AED0]">Ketik minimal 2 huruf untuk mencari.</div>';
}

function closeSearchOnBackdrop(e) {
    if (e.target === gOverlay) closeSearch();
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !gOverlay.classList.contains('hidden')) closeSearch();
});

gSearchInput.addEventListener('input', function() {
    clearTimeout(gDebounce);
    const query = this.value.trim();
    if (query.length < 2) {
        gResultsDiv.innerHTML = '<div class="p-6 text-center text-xs text-[#A3AED0]">Ketik minimal 2 huruf untuk mencari.</div>';
        return;
    }

    gDebounce = setTimeout(() => {
        const q = query.toLowerCase();
        const menuMatches = MENU_ITEMS.filter(m => (m.label + ' ' + m.keywords).toLowerCase().includes(q));

        Promise.all([
            fetch(`/pengasuh/api/search-santri?q=${encodeURIComponent(query)}`).then(r => r.json()),
            fetch(`/pengasuh/api/search-pengurus?q=${encodeURIComponent(query)}`).then(r => r.json()),
        ]).then(([santriList, pengurusList]) => {
            renderGlobalResults(menuMatches, santriList, pengurusList);
        });
    }, 300);
});

function renderGlobalResults(menuMatches, santriList, pengurusList) {
    let html = '';

    if (menuMatches.length > 0) {
        html += `<div class="px-4 pt-3 pb-1 text-[9px] font-bold uppercase tracking-wider text-[#A3AED0]">Menu / Laporan</div>`;
        menuMatches.forEach(m => {
            html += `
                <a href="${m.href}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 no-underline">
                    <div class="w-8 h-8 rounded-full bg-[#4318FF]/10 flex items-center justify-center text-[#4318FF] shrink-0"><i class="fa ${m.icon} text-xs"></i></div>
                    <div class="min-w-0">
                        <p class="font-bold text-[#1B2559] text-xs md:text-sm truncate">${m.label}</p>
                        <p class="text-[10px] text-[#A3AED0]">${m.group}</p>
                    </div>
                </a>`;
        });
    }

    if (santriList.length > 0) {
        html += `<div class="px-4 pt-3 pb-1 text-[9px] font-bold uppercase tracking-wider text-[#A3AED0]">Santri</div>`;
        santriList.forEach(s => {
            html += `
                <a href="/pengasuh/track-record?type=santri&nis=${s.nis}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 no-underline">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0"><i class="fa fa-user-graduate text-xs"></i></div>
                    <div class="min-w-0">
                        <p class="font-bold text-[#1B2559] text-xs md:text-sm truncate">${s.nama}</p>
                        <p class="text-[10px] text-[#A3AED0]">NIS: ${s.nis}</p>
                    </div>
                </a>`;
        });
    }

    if (pengurusList.length > 0) {
        html += `<div class="px-4 pt-3 pb-1 text-[9px] font-bold uppercase tracking-wider text-[#A3AED0]">Pengurus</div>`;
        pengurusList.forEach(p => {
            html += `
                <a href="/pengasuh/track-record?type=pengurus&nis=${p.nis}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 no-underline">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0"><i class="fa fa-user-tie text-xs"></i></div>
                    <div class="min-w-0">
                        <p class="font-bold text-[#1B2559] text-xs md:text-sm truncate">${p.nama}</p>
                        <p class="text-[10px] text-[#A3AED0]">NIS: ${p.nis} · ${p.jabatan}</p>
                    </div>
                </a>`;
        });
    }

    if (!html) {
        html = '<div class="p-6 text-center text-xs text-[#A3AED0]">Tidak ditemukan hasil yang cocok.</div>';
    }

    gResultsDiv.innerHTML = html;
}
</script>
@endpush
