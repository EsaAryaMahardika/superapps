@extends('mahadiyah.layout')

@section('content')
<div class="mt-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-[#1B2559]">Absensi Pengurus</h2>
        <a href="/mahadiyah/absen-pengurus"
            class="bg-[#4318FF] hover:bg-[#3311CC] text-white px-6 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/30">
            <i class="fa fa-plus mr-2"></i>Buat Absensi
        </a>
    </div>

    {{-- Date Picker --}}
    <div id="date-container"
        class="bg-white px-4 py-3 sm:py-2 rounded-xl shadow-sm mb-6 w-full sm:w-fit flex items-center justify-between sm:justify-start gap-4 sm:gap-3 border border-gray-100 cursor-pointer hover:border-blue-200 hover:shadow-md transition-all group relative">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="text-[#4318FF] flex-shrink-0"><i class="fa fa-calendar-alt"></i></div>
            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>
            <input type="text" id="tanggal-display"
                class="bg-transparent border-none text-[#1B2559] font-bold text-sm sm:text-base p-0 flex-1 sm:w-56 pointer-events-none focus:ring-0 text-left"
                placeholder="Pilih Tanggal" readonly>
        </div>
        <i class="fa fa-chevron-down text-[#A3AED0] text-xs transition-transform group-hover:translate-y-0.5 pointer-events-none flex-shrink-0"></i>
        <input type="text" id="tanggal" class="absolute bottom-0 left-0 w-full h-px opacity-0 p-0 border-0 -z-10" readonly>
    </div>

    {{-- Activity Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $activityList = [
                ['id' => 'yasinan',   'tipe' => 'yasinan',   'title' => 'Absensi Yasinan',   'data' => $yasinan],
                ['id' => 'bandongan', 'tipe' => 'bandongan', 'title' => 'Absensi Bandongan', 'data' => $bandongan],
                ['id' => 'wirid',     'tipe' => 'wirid',     'title' => 'Absensi Wirid',     'data' => $wirid],
            ];
        @endphp

        @foreach($activityList as $activity)
        <div class="card h-fit transition-all duration-300 hover:shadow-lg hover:border-blue-100 border border-transparent">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-[#1B2559]">{{ $activity['title'] }}</h3>
                <div class="flex items-center gap-2">
                    {{-- Tombol Edit --}}
                    <a id="edit-btn-{{ $activity['id'] }}" href="#"
                        class="hidden items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#F4F7FE] hover:bg-[#FFB547] text-[#FFB547] hover:text-white text-xs font-semibold transition-all"
                        title="Edit absensi">
                        <i class="fa fa-pen"></i><span>Edit</span>
                    </a>
                    {{-- Tombol Salin --}}
                    <button onclick="salinAbsensi('{{ $activity['tipe'] }}')"
                        class="salin-btn-{{ $activity['id'] }} hidden items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#F4F7FE] hover:bg-[#4318FF] text-[#4318FF] hover:text-white text-xs font-semibold transition-all"
                        title="Salin format teks">
                        <i class="fa fa-copy"></i><span>Salin</span>
                    </button>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                @foreach(['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpa','red']] as $kode=>[$label,$color])
                <div class="bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-lg p-2.5 cursor-pointer hover:bg-{{ $color }}-100 transition-colors"
                    onclick="showStatusList('{{ $activity['id'] }}','{{ $kode }}')">
                    <p class="text-[10px] text-{{ $color }}-600 font-medium mb-0.5">{{ $label }}</p>
                    <p class="text-xl font-bold text-{{ $color }}-700 leading-none" id="count-{{ strtolower($kode === 'A' ? 'alfa' : $label) }}-{{ $activity['id'] }}">0</p>
                </div>
                @endforeach
            </div>

            {{-- Name List (hidden by default) --}}
            <div id="name-list-{{ $activity['id'] }}" class="hidden mb-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-sm font-semibold text-[#1B2559]" id="list-title-{{ $activity['id'] }}">Daftar Nama</h4>
                        <button onclick="hideStatusList('{{ $activity['id'] }}')" class="text-gray-400 hover:text-gray-600">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div id="filtered-names-{{ $activity['id'] }}" class="space-y-2 max-h-64 overflow-y-auto"></div>
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

@endsection

@section('script')
<style>
    @keyframes modalIn { from{opacity:0;transform:scale(.95) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
</style>
<script>
// ── Data dari server ──────────────────────────────────────────
const activitiesData = {
    yasinan:   @json($yasinan->load('pengurus.jabatan.divisi')),
    bandongan: @json($bandongan->load('pengurus.jabatan.divisi')),
    wirid:     @json($wirid->load('pengurus.jabatan.divisi')),
};

const divisiNon   = @json($divisiNon);   // [{id, nama, jabatan:[{pengurus:[]}]}]
const totalSemua  = {{ $totalSemua }};
const totalNon    = {{ $totalNon }};
const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;

// ── State ─────────────────────────────────────────────────────
let currentDate = null;
let editState   = { tipe: null, id: null };

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

    Object.keys(activitiesData).forEach(key => {
        const data  = activitiesData[key];
        const stats = { H:0, S:0, I:0, A:0 };

        data.forEach(item => {
            if (normDate(item.tanggal) === selectedDate && item.status)
                stats[item.status] = (stats[item.status] || 0) + 1;
        });

        document.getElementById(`count-hadir-${key}`).textContent  = stats.H;
        document.getElementById(`count-sakit-${key}`).textContent  = stats.S;
        document.getElementById(`count-izin-${key}`).textContent   = stats.I;
        document.getElementById(`count-alfa-${key}`).textContent   = stats.A;

        const hasData = data.some(i => normDate(i.tanggal) === selectedDate);
        document.querySelector(`.salin-btn-${key}`)?.classList.toggle('hidden', !hasData);
        document.querySelector(`.salin-btn-${key}`)?.classList.toggle('flex', hasData);

        const editBtn = document.getElementById(`edit-btn-${key}`);
        if (editBtn) {
            editBtn.classList.toggle('hidden', !hasData);
            editBtn.classList.toggle('flex', hasData);
            // selectedDate sudah dd-mm-yyyy, langsung pakai untuk URL
            editBtn.href = `/mahadiyah/edit-absen/${key}/${selectedDate}`;
        }

        // Refresh name list jika sedang terbuka
        const nameList = document.getElementById(`name-list-${key}`);
        if (!nameList.classList.contains('hidden')) {
            const title = document.getElementById(`list-title-${key}`).textContent;
            const statusMap = {'Daftar Hadir':'H','Daftar Sakit':'S','Daftar Izin':'I','Daftar Alpa':'A'};
            showStatusList(key, statusMap[title] || 'H');
        }
    });
}

function showStatusList(activityId, status) {
    const nameListDiv     = document.getElementById(`name-list-${activityId}`);
    const filteredNamesDiv = document.getElementById(`filtered-names-${activityId}`);
    const listTitle       = document.getElementById(`list-title-${activityId}`);
    const labels          = { H:'Hadir', S:'Sakit', I:'Izin', A:'Alpa' };

    listTitle.textContent = `Daftar ${labels[status]}`;
    filteredNamesDiv.innerHTML = '';

    const data = activitiesData[activityId];
    let count  = 0;

    data.forEach(item => {
        if (normDate(item.tanggal) !== currentDate || item.status !== status) return;
        count++;
        const jabatan = item.pengurus?.jabatan?.nama ?? '';
        const divisi  = item.pengurus?.jabatan?.divisi?.nama ?? '';
        const sub     = [jabatan, divisi].filter(Boolean).join(' · ');

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

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });
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
    const hariList = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const tglStr = `${hariList[dateObj.getDay()]}, ${dd} ${bulanList[parseInt(mm)-1]} ${yyyy}`;

    // Judul kegiatan
    const judulMap = { yasinan: 'YASINAN', bandongan: 'BANDONGAN', wirid: 'WIRID' };
    const judul = judulMap[tipe] || tipe.toUpperCase();

    // Hitung total hadir
    const hadirList = Object.values(byNis).filter(i => i.status === 'H');
    const izinList  = Object.values(byNis).filter(i => i.status === 'I' || i.status === 'S');
    const totalHadir = hadirList.length;
    const totalPeserta = tipe === 'yasinan' ? totalNon : totalSemua;

    let lines = [];
    lines.push(`PRESENSI ${judul}`);
    lines.push(tglStr);
    lines.push('');
    lines.push('📊 Rekap Kehadiran');
    lines.push(`- Total Kehadiran: ${totalHadir} Pengurus Dari ${totalPeserta} Pengurus`);
    lines.push('');

    if (tipe === 'yasinan') {
        // Hanya Non Kepkam, dikelompokkan per divisi
        lines.push('▶️ Non Kepala Kamar');
        divisiNon.forEach(div => {
            // Kumpulkan semua NIS di divisi ini
            const nisDiv = [];
            div.jabatan.forEach(jab => jab.pengurus.forEach(p => nisDiv.push(p.nis)));

            const hadirDiv = hadirList.filter(i => nisDiv.includes(i.pengurus?.nis));
            const totalDiv = nisDiv.length;

            lines.push(`${div.nama}: *${hadirDiv.length} Pengurus Dari ${totalDiv} Pengurus`);
            if (hadirDiv.length === 0) {
                lines.push('1. -');
            } else {
                hadirDiv.forEach((item, idx) => {
                    const jabatan = item.pengurus?.jabatan?.nama ?? '';
                    const suffix  = jabatan ? ` (${jabatan})` : '';
                    lines.push(`${idx+1}. ${item.pengurus?.nama || '-'}${suffix}`);
                });
            }
        });
    } else {
        // Bandongan & Wirid: semua pengurus, kelompokkan Non Kepkam per divisi dulu
        lines.push('▶️ Non Kepala Kamar');
        divisiNon.forEach(div => {
            const nisDiv = [];
            div.jabatan.forEach(jab => jab.pengurus.forEach(p => nisDiv.push(p.nis)));

            const hadirDiv = hadirList.filter(i => nisDiv.includes(i.pengurus?.nis));
            const totalDiv = nisDiv.length;

            lines.push(`${div.nama}: *${hadirDiv.length} Pengurus Dari ${totalDiv} Pengurus`);
            if (hadirDiv.length === 0) {
                lines.push('1. -');
            } else {
                hadirDiv.forEach((item, idx) => {
                    const jabatan = item.pengurus?.jabatan?.nama ?? '';
                    const suffix  = jabatan ? ` (${jabatan})` : '';
                    lines.push(`${idx+1}. ${item.pengurus?.nama || '-'}${suffix}`);
                });
            }
        });

        // Kepkam
        const hadirKepkam = hadirList.filter(i => {
            const tipeDiv = i.pengurus?.jabatan?.divisi?.tipe;
            return tipeDiv === 'kepkam' || !tipeDiv; // kepkam atau tanpa divisi
        });
        const totalKepkam = totalSemua - totalNon;
        lines.push('');
        lines.push(`▶️ Kepala Kamar: *${hadirKepkam.length} Pengurus Dari ${totalKepkam} Pengurus`);
        if (hadirKepkam.length === 0) {
            lines.push('1. -');
        } else {
            hadirKepkam.forEach((item, idx) => {
                const jabatan = item.pengurus?.jabatan?.nama ?? '';
                const suffix  = jabatan ? ` (${jabatan})` : '';
                lines.push(`${idx+1}. ${item.pengurus?.nama || '-'}${suffix}`);
            });
        }
    }

    // Pengurus Izin/Sakit
    lines.push('');
    lines.push('▶️ Pengurus Izin');
    if (izinList.length === 0) {
        lines.push('1. -');
    } else {
        izinList.forEach((item, idx) => {
            const ket = item.status === 'S' ? 'Sakit' : 'Izin';
            lines.push(`${idx+1}. ${item.pengurus?.nama || '-'} (${ket})`);
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
        days: ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],
        daysShort: ["Min","Sen","Sel","Rab","Kam","Jum","Sab"],
        daysMin: ["Mg","Sn","Sl","Rb","Km","Jm","Sb"],
        months: ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
        monthsShort: ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"],
        today: "Hari Ini", clear: "Kosongkan",
        format: "dd/mm/yyyy", titleFormat: "MM yyyy", weekStart: 1
    };

    const displayInput = $('#tanggal-display');
    const dateInput    = $('#tanggal');
    const options      = { weekday:'long', day:'2-digit', month:'long', year:'numeric' };

    const today = new Date();
    const dd    = String(today.getDate()).padStart(2,'0');
    const mm    = String(today.getMonth()+1).padStart(2,'0');
    const yyyy  = today.getFullYear();
    const dbDate = `${dd}-${mm}-${yyyy}`; // format dd-mm-yyyy sesuai DB

    displayInput.val(today.toLocaleDateString('id-ID', options));
    renderTables(dbDate);

    dateInput.datepicker({
        language: 'id', autoclose: true, format: 'dd/mm/yyyy',
        todayHighlight: true, orientation: 'bottom auto', container: '#date-container'
    }).on('changeDate', function(e) {
        const d   = e.date;
        displayInput.val(d.toLocaleDateString('id-ID', options));
        const day = String(d.getDate()).padStart(2,'0');
        const mo  = String(d.getMonth()+1).padStart(2,'0');
        renderTables(`${day}-${mo}-${d.getFullYear()}`); // dd-mm-yyyy sesuai DB
    });

    dateInput.datepicker('setDate', today);
    $('#date-container').on('click', () => dateInput.datepicker('show'));
});
</script>
@endsection
