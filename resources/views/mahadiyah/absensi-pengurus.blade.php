@extends('mahadiyah.layout')

@section('content')
    <div class="mt-2 sm:mt-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
            <h2 class="text-xl sm:text-2xl font-bold text-[#1B2559]">Absensi Pengurus</h2>
            <a href="/mahadiyah/absen-pengurus"
                class="w-full sm:w-auto text-center bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30 text-sm">
                <i class="fa fa-plus mr-2"></i>Buat Absensi
            </a>
        </div>

        {{-- Date Picker --}}
        <div id="date-container"
            class="bg-white px-4 py-3 sm:py-2 rounded-xl shadow-sm mb-4 sm:mb-6 w-full sm:w-fit flex items-center justify-between sm:justify-start gap-3 border border-gray-100 cursor-pointer hover:border-blue-200 hover:shadow-md transition-all group relative">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="text-[#4318FF] flex-shrink-0 text-sm"><i class="fa fa-calendar-alt"></i></div>
                <div class="h-5 w-px bg-gray-200 hidden sm:block"></div>
                <input type="text" id="tanggal-display"
                    class="bg-transparent border-none text-[#1B2559] font-bold text-sm p-0 flex-1 sm:w-56 pointer-events-none focus:ring-0 text-left"
                    placeholder="Pilih Tanggal" readonly>
            </div>
            <i class="fa fa-chevron-down text-[#A3AED0] text-xs pointer-events-none flex-shrink-0"></i>
            <input type="text" id="tanggal" class="absolute bottom-0 left-0 w-full h-px opacity-0 p-0 border-0 -z-10" readonly>
        </div>

        {{-- Activity Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            @php
                $activityList = [
                    ['id' => 'yasinan', 'tipe' => 'yasinan', 'title' => 'Absensi Yasinan', 'data' => $yasinan],
                    ['id' => 'bandongan', 'tipe' => 'bandongan', 'title' => 'Absensi Bandongan', 'data' => $bandongan],
                    ['id' => 'wirid', 'tipe' => 'wirid', 'title' => 'Absensi Wirid', 'data' => $wirid],
                ];
            @endphp

            @foreach($activityList as $activity)
                <div class="card h-fit transition-all duration-300 hover:shadow-lg hover:border-blue-100 border border-transparent !p-4 sm:!p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                        <h3 class="text-base sm:text-lg font-bold text-[#1B2559]">{{ $activity['title'] }}</h3>
                        <div class="flex flex-wrap items-center gap-1.5">
                            {{-- Badge Libur (tampil via JS) --}}
                            <span id="libur-badge-{{ $activity['id'] }}"
                                class="hidden items-center gap-1 px-2 py-1 rounded-lg bg-purple-100 text-purple-700 text-xs font-bold">
                                <i class="fa fa-moon"></i> LIBUR
                            </span>
                            {{-- Tombol Set/Hapus Libur --}}
                            <button id="libur-btn-{{ $activity['id'] }}"
                                onclick="toggleLibur('{{ $activity['tipe'] }}')"
                                class="hidden items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#F4F7FE] hover:bg-purple-500 text-purple-500 hover:text-white text-xs font-semibold transition-all"
                                title="Tandai libur">
                                <i class="fa fa-moon text-[10px]"></i>
                                <span class="hidden sm:inline">Libur</span>
                            </button>
                            {{-- Tombol Edit --}}
                            <a id="edit-btn-{{ $activity['id'] }}" href="#"
                                class="hidden items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#F4F7FE] hover:bg-[#FFB547] text-[#FFB547] hover:text-white text-xs font-semibold transition-all"
                                title="Edit absensi">
                                <i class="fa fa-pen text-[10px]"></i>
                                <span class="hidden sm:inline">Edit</span>
                            </a>
                            {{-- Tombol Salin --}}
                            <button onclick="salinAbsensi('{{ $activity['tipe'] }}')"
                                class="salin-btn-{{ $activity['id'] }} hidden items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#F4F7FE] hover:bg-[#4318FF] text-[#4318FF] hover:text-white text-xs font-semibold transition-all"
                                title="Salin format teks">
                                <i class="fa fa-copy text-[10px]"></i>
                                <span class="hidden sm:inline">Salin</span>
                            </button>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-4 gap-1.5 sm:gap-2 mb-4">
                        @foreach(['H' => ['Hadir', 'green'], 'S' => ['Sakit', 'yellow'], 'I' => ['Izin', 'blue'], 'A' => ['Alpa', 'red']] as $kode => [$label, $color])
                            <div class="bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-lg p-2 sm:p-2.5 cursor-pointer hover:bg-{{ $color }}-100 transition-colors text-center"
                                onclick="showStatusList('{{ $activity['id'] }}','{{ $kode }}')">
                                <p class="text-[9px] sm:text-[10px] text-{{ $color }}-600 font-medium mb-0.5">{{ $label }}</p>
                                <p class="text-lg sm:text-xl font-bold text-{{ $color }}-700 leading-none"
                                    id="count-{{ strtolower($kode === 'A' ? 'alfa' : $label) }}-{{ $activity['id'] }}">0</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Name List (hidden by default) --}}
                    <div id="name-list-{{ $activity['id'] }}" class="hidden mb-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 sm:p-4">
                            <div class="flex justify-between items-center mb-2 sm:mb-3">
                                <h4 class="text-xs sm:text-sm font-semibold text-[#1B2559]" id="list-title-{{ $activity['id'] }}">Daftar Nama</h4>
                                <button onclick="hideStatusList('{{ $activity['id'] }}')"
                                    class="text-gray-400 hover:text-gray-600 w-6 h-6 flex items-center justify-center">
                                    <i class="fa fa-times text-xs"></i>
                                </button>
                            </div>
                            <div id="filtered-names-{{ $activity['id'] }}" class="space-y-1.5 max-h-48 sm:max-h-64 overflow-y-auto"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Toast notif --}}
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
        <div class="bg-[#1B2559] text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2">
            <i class="fa fa-check-circle text-[#05CD99]"></i>
            <span id="toast-msg">Berhasil</span>
        </div>
    </div>

    {{-- Modal Input Keterangan Libur --}}
    <div id="modal-libur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4" style="animation: modalIn .2s ease">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fa fa-moon text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-[#1B2559] text-sm" id="modal-libur-title">Tandai Libur</h3>
                    <p class="text-[11px] text-gray-400" id="modal-libur-sub"></p>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Keterangan (opsional)</label>
                <input type="text" id="libur-keterangan" maxlength="255"
                    class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all"
                    placeholder="Contoh: Hari Raya, Libur Nasional...">
            </div>
            <div class="flex gap-2">
                <button onclick="submitLibur()"
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-xl font-semibold text-sm transition-all">
                    <i class="fa fa-moon mr-1"></i> Simpan
                </button>
                <button onclick="closeLiburModal()"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-semibold text-sm transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- Form tersembunyi untuk POST/DELETE libur --}}
    <form id="form-libur-store" method="POST" action="/mahadiyah/libur-pengurus" class="hidden">
        @csrf
        <input type="hidden" name="tanggal" id="libur-form-tanggal">
        <input type="hidden" name="tipe" id="libur-form-tipe">
        <input type="hidden" name="keterangan" id="libur-form-keterangan">
    </form>
    <form id="form-libur-destroy" method="POST" action="/mahadiyah/libur-pengurus" class="hidden">
        @csrf
        @method('DELETE')
        <input type="hidden" name="tanggal" id="libur-destroy-tanggal">
        <input type="hidden" name="tipe" id="libur-destroy-tipe">
    </form>

@endsection

@section('script')
    <style>
        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(.95) translateY(8px)
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0)
            }
        }
    </style>
    <script>
        // ── Data dari server ──────────────────────────────────────────
        const activitiesData = {
            yasinan: @json($yasinan->load('pengurus.jabatan.divisi')),
            bandongan: @json($bandongan->load('pengurus.jabatan.divisi')),
            wirid: @json($wirid->load('pengurus.jabatan.divisi')),
        };

        const divisiNon = @json($divisiNon);
        const divisiKepkam = @json($divisiKepkam);
        const totalSemua = {{ $totalSemua }};
        const totalNon = {{ $totalNon }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Data libur dari server: { "dd-mm-yyyy": { "bandongan": "keterangan", ... }, ... }
        const semuaLibur = @json($semuaLibur);

        // ── State ─────────────────────────────────────────────────────
        let currentDate = null;
        let editState = { tipe: null, id: null };

        // ── Helpers ───────────────────────────────────────────────────
        function normDate(d) { return d ? d.replace(/\//g, '-') : ''; }

        // Bandingkan tanggal — data dari DB sudah dd-mm-yyyy, selectedDate juga dd-mm-yyyy
        function sameDate(itemDate, selected) {
            return normDate(itemDate) === selected;
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 2500);
        }

        // ── Render stats & name list ──────────────────────────────────
        function renderTables(selectedDate) {
            if (!selectedDate) return;
            currentDate = selectedDate;

            // Ambil data libur untuk tanggal ini
            const liburHari = semuaLibur[selectedDate] || {};

            Object.keys(activitiesData).forEach(key => {
                const data     = activitiesData[key];
                const isLibur  = liburHari.hasOwnProperty(key);
                const stats    = { H: 0, S: 0, I: 0, A: 0 };

                data.forEach(item => {
                    if (normDate(item.tanggal) === selectedDate && item.status)
                        stats[item.status] = (stats[item.status] || 0) + 1;
                });

                document.getElementById(`count-hadir-${key}`).textContent = isLibur ? '-' : stats.H;
                document.getElementById(`count-sakit-${key}`).textContent = isLibur ? '-' : stats.S;
                document.getElementById(`count-izin-${key}`).textContent  = isLibur ? '-' : stats.I;
                document.getElementById(`count-alfa-${key}`).textContent  = isLibur ? '-' : stats.A;

                // Badge libur
                const liburBadge = document.getElementById(`libur-badge-${key}`);
                if (liburBadge) {
                    liburBadge.classList.toggle('hidden', !isLibur);
                    liburBadge.classList.toggle('flex', isLibur);
                    if (isLibur && liburHari[key]) {
                        liburBadge.title = 'Keterangan: ' + liburHari[key];
                    }
                }

                // Tombol libur
                const liburBtn = document.getElementById(`libur-btn-${key}`);
                if (liburBtn) {
                    liburBtn.classList.remove('hidden');
                    liburBtn.classList.add('flex');
                    if (isLibur) {
                        liburBtn.classList.remove('text-purple-500', 'hover:bg-purple-500');
                        liburBtn.classList.add('text-red-500', 'hover:bg-red-500');
                        liburBtn.querySelector('span').textContent = 'Batal Libur';
                        liburBtn.title = 'Batalkan libur';
                    } else {
                        liburBtn.classList.remove('text-red-500', 'hover:bg-red-500');
                        liburBtn.classList.add('text-purple-500', 'hover:bg-purple-500');
                        liburBtn.querySelector('span').textContent = 'Libur';
                        liburBtn.title = 'Tandai libur';
                    }
                }

                const hasData = data.some(i => normDate(i.tanggal) === selectedDate);
                document.querySelector(`.salin-btn-${key}`)?.classList.toggle('hidden', !hasData);
                document.querySelector(`.salin-btn-${key}`)?.classList.toggle('flex', hasData);

                const editBtn = document.getElementById(`edit-btn-${key}`);
                if (editBtn) {
                    editBtn.classList.toggle('hidden', !hasData);
                    editBtn.classList.toggle('flex', hasData);
                    editBtn.href = `/mahadiyah/edit-absen/${key}/${selectedDate}`;
                }

                // Refresh name list jika sedang terbuka
                const nameList = document.getElementById(`name-list-${key}`);
                if (!nameList.classList.contains('hidden')) {
                    const title   = document.getElementById(`list-title-${key}`).textContent;
                    const statusMap = { 'Daftar Hadir': 'H', 'Daftar Sakit': 'S', 'Daftar Izin': 'I', 'Daftar Alpa': 'A' };
                    showStatusList(key, statusMap[title] || 'H');
                }
            });
        }

        function showStatusList(activityId, status) {
            const nameListDiv = document.getElementById(`name-list-${activityId}`);
            const filteredNamesDiv = document.getElementById(`filtered-names-${activityId}`);
            const listTitle = document.getElementById(`list-title-${activityId}`);
            const labels = { H: 'Hadir', S: 'Sakit', I: 'Izin', A: 'Alpa' };

            listTitle.textContent = `Daftar ${labels[status]}`;
            filteredNamesDiv.innerHTML = '';

            const data = activitiesData[activityId];
            let count = 0;

            data.forEach(item => {
                if (normDate(item.tanggal) !== currentDate || item.status !== status) return;
                count++;
                const jabatan = item.pengurus?.jabatan?.nama ?? '';
                const divisi = item.pengurus?.jabatan?.divisi?.nama ?? '';
                const sub = [jabatan, divisi].filter(Boolean).join(' · ');

                filteredNamesDiv.insertAdjacentHTML('beforeend', `
                        <div class="flex items-center gap-2 p-2 bg-white rounded border border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-[#F4F7FE] flex items-center justify-center flex-shrink-0">
                                <i class="fa fa-user text-[#4318FF] text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-[#1B2559] truncate">${item.pengurus?.nama || '-'}</p>
                                ${sub ? `<p class="text-xs text-gray-400 truncate">${sub}</p>` : ''}
                            </div>
                        </div>`);
            });

            if (count === 0)
                filteredNamesDiv.innerHTML = '<p class="text-sm text-gray-400 italic text-center py-4">Tidak ada data</p>';

            nameListDiv.classList.remove('hidden');
        }

        function hideStatusList(activityId) {
            document.getElementById(`name-list-${activityId}`).classList.add('hidden');
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeLiburModal(); closeEditModal(); } });
    </script>

    <script>
        // ── Libur Pengurus ────────────────────────────────────────────
        let liburTipe = null;

        function toggleLibur(tipe) {
            if (!currentDate) return;
            const liburHari = semuaLibur[currentDate] || {};
            const isLibur   = liburHari.hasOwnProperty(tipe);

            if (isLibur) {
                // Langsung hapus libur
                document.getElementById('libur-destroy-tanggal').value = currentDate;
                document.getElementById('libur-destroy-tipe').value    = tipe;
                document.getElementById('form-libur-destroy').submit();
            } else {
                // Buka modal input keterangan
                liburTipe = tipe;
                const namaMap = { bandongan: 'Bandongan', wirid: 'Wirid', yasinan: 'Yasinan' };
                const [dd, mm, yyyy] = currentDate.split('-');
                document.getElementById('modal-libur-title').textContent = `Tandai Libur ${namaMap[tipe]}`;
                document.getElementById('modal-libur-sub').textContent   = `${dd}-${mm}-${yyyy}`;
                document.getElementById('libur-keterangan').value = '';
                const modal = document.getElementById('modal-libur');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.getElementById('libur-keterangan').focus();
            }
        }

        function submitLibur() {
            if (!liburTipe || !currentDate) return;
            document.getElementById('libur-form-tanggal').value    = currentDate;
            document.getElementById('libur-form-tipe').value       = liburTipe;
            document.getElementById('libur-form-keterangan').value = document.getElementById('libur-keterangan').value;
            document.getElementById('form-libur-store').submit();
        }

        function closeLiburModal() {
            const modal = document.getElementById('modal-libur');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            liburTipe = null;
        }

        // Enter di input keterangan → submit
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('libur-keterangan')?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') submitLibur();
            });
        });
    </script>

    <script>
        // ── Salin Absensi ─────────────────────────────────────────────
        function salinAbsensi(tipe) {
            const data = activitiesData[tipe];
            if (!data || !currentDate) return;

            // Filter data tanggal ini
            const byNis = {};
            data.forEach(item => {
                if (normDate(item.tanggal) === currentDate)
                    byNis[item.pengurus?.nis] = item;
            });

            // Format tanggal Indonesia
            const [dd, mm, yyyy] = currentDate.split('-'); // dd-mm-yyyy
            const dateObj = new Date(`${yyyy}-${mm}-${dd}`);
            const hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const tglStr = `${hariList[dateObj.getDay()]}, ${dd} ${bulanList[parseInt(mm) - 1]} ${yyyy}`;

            // Judul kegiatan
            const judulMap = { yasinan: 'JAMAAH SUBUH DAN YASIN', bandongan: 'BANDONGAN', wirid: 'WIRID' };
            const judul = judulMap[tipe] || tipe.toUpperCase();

            const emojiMap = { yasinan: '📖', bandongan: '📕', wirid: '📿' };
            const emoji = emojiMap[tipe] || '📕';

            // Hitung total hadir
            const hadirList = Object.values(byNis).filter(i => i.status === 'H');
            const izinList = Object.values(byNis).filter(i => i.status === 'I' || i.status === 'S');
            const totalHadir = hadirList.length;
            const totalPeserta = tipe === 'yasinan' ? totalNon : totalSemua;

            let lines = [];
            lines.push(`*${emoji} PRESENSI ${judul}*`);
            lines.push(`_${tglStr}_`);
            lines.push('');
            lines.push('*📊 Rekap Kehadiran*');
            const presentase = totalPeserta > 0 ? Math.round((totalHadir / totalPeserta) * 100) : 0;
            lines.push(`- Total Kehadiran: *${totalHadir} Pengurus Dari ${totalPeserta} Pengurus (${presentase}%)*`);
            lines.push('');

            if (tipe === 'yasinan') {
                // Hanya Non Kepkam, dikelompokkan per divisi
                const hadirNon = totalHadir;
                lines.push(`*▶️ Non Kepala Kamar:*`);
                divisiNon.forEach(div => {
                    // Kumpulkan semua NIS di divisi ini
                    const nisDiv = [];
                    div.jabatan.forEach(jab => jab.pengurus.forEach(p => nisDiv.push(p.nis)));

                    const hadirDiv = hadirList.filter(i => nisDiv.includes(i.pengurus?.nis));
                    const totalDiv = nisDiv.length;

                    lines.push(`*${div.nama}: ${hadirDiv.length} Pengurus Dari ${totalDiv} Pengurus*`);
                    if (hadirDiv.length === 0) {
                        lines.push('1. -');
                    } else {
                        hadirDiv.forEach((item, idx) => {
                            const jabatan = item.pengurus?.jabatan?.nama ?? '';
                            const suffix = jabatan ? ` (${jabatan})` : '';
                            lines.push(`${idx + 1}. ${item.pengurus?.nama || '-'}${suffix}`);
                        });
                        lines.push('');
                    }
                });
            } else {
                // Bandongan & Wirid: semua pengurus, kelompokkan Kepkam dulu baru Non Kepkam
                // Kepkam
                const hadirKepkam = hadirList.filter(i => {
                    const tipeDiv = i.pengurus?.jabatan?.divisi?.tipe;
                    return tipeDiv === 'kepkam' || !tipeDiv; // kepkam atau tanpa divisi
                });
                const totalKepkam = totalSemua - totalNon;

                const presentaseKepkam = totalKepkam > 0 ? Math.round((hadirKepkam.length / totalKepkam) * 100) : 0;
                lines.push(`*▶️ Kepala Kamar: ${hadirKepkam.length} Pengurus Dari ${totalKepkam} Pengurus (${presentaseKepkam}%)*`);
                if (hadirKepkam.length === 0) {
                    lines.push('1. -');
                    lines.push('');
                } else {
                    const printedNis = new Set();
                    divisiKepkam.forEach(div => {
                        const nisDiv = [];
                        div.jabatan.forEach(jab => jab.pengurus.forEach(p => nisDiv.push(p.nis)));

                        const hadirDiv = hadirKepkam.filter(i => nisDiv.includes(i.pengurus?.nis));
                        const totalDiv = nisDiv.length;

                        if (hadirDiv.length > 0) {
                            lines.push(`*${div.nama}: ${hadirDiv.length} Pengurus Dari ${totalDiv} Pengurus*`);
                            hadirDiv.forEach((item, idx) => {
                                printedNis.add(item.pengurus?.nis);
                                const jabatan = item.pengurus?.jabatan?.nama ?? '';
                                lines.push(`${idx + 1}. ${item.pengurus?.nama || '-'}`);
                            });
                            lines.push('');
                        }
                    });

                    // Fallback for any present Kepala Kamar not in the divisions
                    const remainingHadir = hadirKepkam.filter(i => !printedNis.has(i.pengurus?.nis));
                    if (remainingHadir.length > 0) {
                        lines.push(`*Tanpa Divisi: ${remainingHadir.length} Pengurus*`);
                        remainingHadir.forEach((item, idx) => {
                            const jabatan = item.pengurus?.jabatan?.nama ?? '';
                            const suffix = jabatan ? ` (${jabatan})` : '';
                            lines.push(`${idx + 1}. ${item.pengurus?.nama || '-'}${suffix}`);
                        });
                        lines.push('');
                    }
                }

                // Non Kepkam
                // Ensure there is exactly one empty line before Non Kepala Kamar
                if (lines[lines.length - 1] !== '') {
                    lines.push('');
                }
                const hadirNon = totalHadir - hadirKepkam.length;
                const presentaseNon = totalNon > 0 ? Math.round((hadirNon / totalNon) * 100) : 0;
                lines.push(`*▶️ Non Kepala Kamar: ${hadirNon} Pengurus Dari ${totalNon} Pengurus (${presentaseNon}%)*`);
                divisiNon.forEach(div => {
                    const nisDiv = [];
                    div.jabatan.forEach(jab => jab.pengurus.forEach(p => nisDiv.push(p.nis)));

                    const hadirDiv = hadirList.filter(i => nisDiv.includes(i.pengurus?.nis));
                    const totalDiv = nisDiv.length;

                    lines.push(`*${div.nama}: ${hadirDiv.length} Pengurus Dari ${totalDiv} Pengurus*`);
                    if (hadirDiv.length === 0) {
                        lines.push('1. -');
                    } else {
                        hadirDiv.forEach((item, idx) => {
                            const jabatan = item.pengurus?.jabatan?.nama ?? '';
                            const suffix = jabatan ? ` (${jabatan})` : '';
                            lines.push(`${idx + 1}. ${item.pengurus?.nama || '-'}${suffix}`);
                        });
                        lines.push('');
                    }
                });
            }

            // Pengurus Izin/Sakit
            // Ensure there is exactly one empty line before Pengurus Izin
            if (lines[lines.length - 1] !== '') {
                lines.push('');
            }
            lines.push('▶️ Pengurus Izin');
            if (izinList.length === 0) {
                lines.push('1. -');
            } else {
                izinList.forEach((item, idx) => {
                    const ket = item.status === 'S' ? 'Sakit' : 'Izin';
                    lines.push(`${idx + 1}. ${item.pengurus?.nama || '-'} (${ket})`);
                });
            }

            const teks = lines.join('\n');

            navigator.clipboard.writeText(teks)
                .then(() => showToast('Format absensi berhasil disalin!'))
                .catch(() => {
                    // Fallback untuk browser yang tidak support clipboard API
                    const ta = document.createElement('textarea');
                    ta.value = teks;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    showToast('Format absensi berhasil disalin!');
                });
        }

        // ── Datepicker ────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            $.fn.datepicker.dates['id'] = {
                days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
                daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                daysMin: ["Mg", "Sn", "Sl", "Rb", "Km", "Jm", "Sb"],
                months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
                monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                today: "Hari Ini", clear: "Kosongkan",
                format: "dd/mm/yyyy", titleFormat: "MM yyyy", weekStart: 1
            };

            const displayInput = $('#tanggal-display');
            const dateInput = $('#tanggal');
            const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };

            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            const dbDate = `${dd}-${mm}-${yyyy}`; // format dd-mm-yyyy sesuai DB

            displayInput.val(today.toLocaleDateString('id-ID', options));
            renderTables(dbDate);

            dateInput.datepicker({
                language: 'id', autoclose: true, format: 'dd/mm/yyyy',
                todayHighlight: true, orientation: 'bottom auto', container: '#date-container'
            }).on('changeDate', function (e) {
                const d = e.date;
                displayInput.val(d.toLocaleDateString('id-ID', options));
                const day = String(d.getDate()).padStart(2, '0');
                const mo = String(d.getMonth() + 1).padStart(2, '0');
                renderTables(`${day}-${mo}-${d.getFullYear()}`); // dd-mm-yyyy sesuai DB
            });

            dateInput.datepicker('setDate', today);
            $('#date-container').on('click', () => dateInput.datepicker('show'));
        });
    </script>
@endsection