@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-[#1B2559]">Buat Absensi Pengurus</h2>
            <div class="flex gap-3">
                <a href="/mahadiyah/absensi-pengurus"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition-all">
                    <i class="fa fa-times mr-2"></i>Batal
                </a>
                <button type="submit" form="formAbsensi"
                    class="px-6 py-2.5 rounded-xl bg-[#4318FF] hover:bg-[#3311CC] text-white font-semibold transition-all shadow-lg shadow-blue-500/30">
                    <i class="fa fa-save mr-2"></i>Simpan Absensi
                </button>
            </div>
        </div>

        <form action="/mahadiyah/absen-pengurus" method="post" id="formAbsensi">
            @csrf

            <!-- Form Header Card -->
            <div class="card mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-[#1B2559] mb-2">
                            <i class="fa fa-clipboard-list mr-2 text-[#4318FF]"></i>Jenis Kegiatan
                        </label>
                        <select id="selectKegiatan"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-[#1B2559] font-medium"
                            name="kegiatan" required>
                            @foreach ($kegiatan as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#1B2559] mb-2">
                            <i class="fa fa-calendar mr-2 text-[#4318FF]"></i>Tanggal
                        </label>
                        <div class="relative">
                            <input type="text" id="tanggal-absen" data-provide="datepicker"
                                data-date-autoclose="true" data-date-format="dd-mm-yyyy"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-[#1B2559] font-medium cursor-pointer"
                                name="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i class="fa fa-calendar-alt text-[#A3AED0]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table Card -->
            <div class="card">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-[#1B2559]">Daftar Pengurus</h3>
                        <p id="info-peserta" class="text-xs text-[#A3AED0] mt-0.5"></p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:flex-initial">
                            <input type="text" id="searchInput" placeholder="Cari nama pengurus..."
                                class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all text-sm">
                            <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="setAllStatus('H')"
                                class="px-3 py-2 text-xs font-semibold rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors whitespace-nowrap">
                                <i class="fa fa-check mr-1"></i>Semua Hadir
                            </button>
                            <button type="button" onclick="setAllStatus('A')"
                                class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors whitespace-nowrap">
                                <i class="fa fa-times mr-1"></i>Semua Alpa
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr class="text-left text-sm text-[#A3AED0] border-b border-gray-100">
                                <th class="pb-3 font-semibold">Nama Pengurus</th>
                                <th class="pb-3 font-semibold hidden md:table-cell">Jabatan</th>
                                <th class="pb-3 font-semibold">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pengurus" class="text-sm">
                            {{-- diisi oleh JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
<script>
    // Data dari server
    const pengurusSemua = @json($pengurusSemua);
    const pengurusNon   = @json($pengurusNon);

    // ID kegiatan yasinan — ambil dari data kegiatan
    const kegiatanData  = @json($kegiatan->keyBy('id'));

    // Kegiatan yang hanya untuk non kepkam (yasinan = id 9)
    const YASINAN_ID = '9';

    function getListPengurus(kegiatanId) {
        return String(kegiatanId) === YASINAN_ID ? pengurusNon : pengurusSemua;
    }

    function renderTable(kegiatanId) {
        const list   = getListPengurus(kegiatanId);
        const tbody  = document.getElementById('tbody-pengurus');
        const info   = document.getElementById('info-peserta');
        const isYasinan = String(kegiatanId) === YASINAN_ID;

        info.innerHTML = isYasinan
            ? `<span class="inline-flex items-center gap-1 text-[#05CD99]"><i class="fa fa-info-circle"></i> Yasinan: hanya pengurus Non Kepala Kamar (${list.length} orang)</span>`
            : `<span class="inline-flex items-center gap-1"><i class="fa fa-users"></i> Semua pengurus (${list.length} orang)</span>`;

         tbody.innerHTML = list.map(p => {
            const jabatan = p.jabatan?.nama ?? '-';
            const divisi  = p.jabatan?.divisi?.nama ?? '';
            const jabLabel = divisi ? `${jabatan} · <span class="text-[#4318FF]">${divisi}</span>` : jabatan;
            const jabLabelPlain = divisi ? `${jabatan} · ${divisi}` : jabatan;

            return `
            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors pengurus-row">
                <td class="py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#F4F7FE] flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-user text-[#4318FF]"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-[#1B2559] nama-col">${p.nama}</p>
                            <div class="flex flex-col gap-0.5">
                                <p class="text-xs text-gray-400">NIS: ${p.nis}</p>
                                <p class="text-xs text-gray-500 md:hidden font-medium">${jabLabel}</p>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="py-4 hidden md:table-cell">
                    <p class="text-sm text-[#1B2559] jabatan-col">${jabLabel}</p>
                </td>
                <td class="py-4">
                    <select name="pengurus[${p.nis}]"
                        class="status-select px-4 py-2.5 rounded-lg border border-gray-200 focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all font-medium min-w-[120px] md:min-w-[150px] text-green-600 border-green-200 bg-green-50">
                        <option value="H" selected>Hadir</option>
                        <option value="S">Sakit</option>
                        <option value="I">Izin</option>
                        <option value="A">Alpa</option>
                    </select>
                </td>
            </tr>`;
        }).join('');

        // Re-attach color + search listeners
        attachSelectListeners();
        document.getElementById('searchInput').value = '';
    }

    function attachSelectListeners() {
        document.querySelectorAll('.status-select').forEach(select => {
            updateSelectColor(select);
            select.addEventListener('change', function () { updateSelectColor(this); });
        });
    }

    function setAllStatus(status) {
        document.querySelectorAll('.status-select').forEach(select => {
            select.value = status;
            updateSelectColor(select);
        });
    }

    function updateSelectColor(select) {
        const colors = {
            'H': 'text-green-600 border-green-200 bg-green-50',
            'S': 'text-yellow-600 border-yellow-200 bg-yellow-50',
            'I': 'text-blue-600 border-blue-200 bg-blue-50',
            'A': 'text-red-600 border-red-200 bg-red-50'
        };
        select.className = 'status-select px-4 py-2.5 rounded-lg border focus:border-[#4318FF] focus:ring-2 focus:ring-[#4318FF]/20 transition-all font-medium min-w-[150px]';
        if (colors[select.value]) select.className += ' ' + colors[select.value];
    }

    // Ganti tabel saat kegiatan berubah
    document.getElementById('selectKegiatan').addEventListener('change', function () {
        renderTable(this.value);
    });

    // Search
    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.pengurus-row').forEach(row => {
            const nama    = row.querySelector('.nama-col')?.textContent.toLowerCase() ?? '';
            const jabatan = row.querySelector('.jabatan-col')?.textContent.toLowerCase() ?? '';
            row.style.display = (nama + jabatan).includes(q) ? '' : 'none';
        });
    });

    // Init datepicker
    $('#tanggal-absen').datepicker({ format: 'dd-mm-yyyy', autoclose: true, todayHighlight: true, orientation: 'bottom auto' });

    // Render awal
    renderTable(document.getElementById('selectKegiatan').value);
</script>
@endsection
