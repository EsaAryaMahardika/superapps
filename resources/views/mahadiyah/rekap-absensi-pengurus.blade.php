@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-[#1B2559]">Rekap Absensi Pengurus</h2>
                <p class="text-[#A3AED0] text-xs md:text-sm mt-1">Pantau keaktifan kehadiran pengurus di kegiatan Bandongan, Wirid, dan Yasinan</p>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-6 border border-gray-100 shadow-sm rounded-2xl bg-white p-5">
            <form method="GET" action="/mahadiyah/rekap-absensi-pengurus"
                class="flex flex-col md:flex-row gap-4 items-stretch md:items-end w-full">
                <div class="w-full md:flex-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1B2559] mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}" class="w-full bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-11 px-4 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all">
                </div>
                <div class="w-full md:flex-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1B2559] mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}" class="w-full bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-11 px-4 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all">
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit"
                        class="btn bg-[#4318FF] text-white hover:bg-[#3311CC] px-6 py-2.5 rounded-xl font-semibold transition-all shadow-md shadow-blue-500/20 flex-1 md:flex-initial flex justify-center items-center gap-2">
                        <i class="fa fa-search"></i>
                        <span>Tampilkan</span>
                    </button>
                    <a href="/mahadiyah/rekap-absensi-pengurus" 
                        class="btn bg-gray-50 border border-gray-200 text-gray-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-all w-auto flex justify-center items-center"
                        title="Reset filter">
                        <i class="fa fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Download & Filter Tabs Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
            <!-- Tabs Filter Jenis Pengurus (Client Side JS) -->
            <div class="flex bg-gray-100/80 p-1.5 rounded-xl gap-1 w-full lg:w-auto overflow-x-auto whitespace-nowrap">
                <button onclick="filterTipe('all')" id="tab-all"
                    class="tab-btn flex-1 lg:flex-none text-xs font-semibold px-4 py-2 rounded-lg transition-all bg-[#4318FF] text-white">
                    Semua Pengurus
                </button>
                <button onclick="filterTipe('non')" id="tab-non"
                    class="tab-btn flex-1 lg:flex-none text-xs font-semibold px-4 py-2 rounded-lg transition-all bg-white text-[#2B3674] border border-transparent">
                    Non Kepala Kamar
                </button>
                <button onclick="filterTipe('kepkam')" id="tab-kepkam"
                    class="tab-btn flex-1 lg:flex-none text-xs font-semibold px-4 py-2 rounded-lg transition-all bg-white text-[#2B3674] border border-transparent">
                    Kepala Kamar
                </button>
            </div>

            <!-- Download Buttons -->
            <div class="flex flex-wrap gap-2 w-full lg:w-auto justify-start lg:justify-end">
                <button onclick="downloadExcel()"
                    class="btn bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] shadow-sm rounded-xl font-semibold py-2.5 px-4 flex items-center justify-center gap-2 text-xs flex-1 lg:flex-none">
                    <i class="fa fa-file-excel text-green-600"></i>
                    <span>Download Excel</span>
                </button>
                <button onclick="downloadPdf()"
                    class="btn bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] shadow-sm rounded-xl font-semibold py-2.5 px-4 flex items-center justify-center gap-2 text-xs flex-1 lg:flex-none">
                    <i class="fa fa-file-pdf text-red-500"></i>
                    <span>Download PDF</span>
                </button>
            </div>
        </div>

        <!-- Recap Table Card -->
        <div class="card bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden p-5">
            <div class="mb-5 pb-5 border-b border-gray-100 text-center">
                <h2 class="text-lg font-bold text-[#1B2559] mb-1">Rekap Absensi Pengurus</h2>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Pondok Pesantren An-Nur II "Al-Murtadlo"</h3>
                <p class="text-xs text-gray-400">
                    <span class="font-semibold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full">
                        Periode: {{ $startDate->locale('id')->isoFormat('DD MMMM YYYY') }} - {{ $endDate->locale('id')->isoFormat('DD MMMM YYYY') }}
                    </span>
                </p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full border-collapse" style="text-align: center;">
                    <thead>
                        <tr class="bg-[#4318FF]">
                            <th class="text-[11px] uppercase tracking-wider text-white font-bold p-3.5 border border-[#3311CC] sticky left-0 bg-[#4318FF] z-20 text-left w-[130px] md:w-[200px] min-w-[130px] md:min-w-[200px] max-w-[130px] md:max-w-[200px]">
                                Pengurus
                            </th>
                            @foreach($dates as $date)
                                @php
                                    $dateObj = \Carbon\Carbon::createFromFormat('d-m-Y', $date);
                                    $hari = $dateObj->locale('id')->isoFormat('dd');
                                @endphp
                                <th class="text-[10px] text-white font-bold p-2 border border-[#3311CC] text-center min-w-[70px]">
                                    <span class="block text-[8px] uppercase tracking-wider text-blue-200 font-medium">{{ $hari }}</span>
                                    <span class="block text-xs font-extrabold mt-0.5">{{ $dateObj->format('d') }}</span>
                                </th>
                            @endforeach
                            <th class="text-[10px] uppercase tracking-wider text-white font-bold p-3.5 border border-[#3311CC] text-center min-w-[80px] bg-[#3311CC]">
                                Bandongan
                            </th>
                            <th class="text-[10px] uppercase tracking-wider text-white font-bold p-3.5 border border-[#3311CC] text-center min-w-[80px] bg-[#3311CC]">
                                Wirid
                            </th>
                            <th class="text-[10px] uppercase tracking-wider text-white font-bold p-3.5 border border-[#3311CC] text-center min-w-[80px] bg-[#3311CC]">
                                Yasinan
                            </th>
                        </tr>
                        <!-- Baris Total Hadir per Tanggal — 3 versi per tipe -->
                        @if(count($rekapData) > 0)
                        @foreach(['all','kepkam','non'] as $tipeRow)
                        <tr class="bg-[#1B2559] text-white text-[10px] total-hadir-row total-hadir-{{ $tipeRow }}"
                            style="{{ $tipeRow !== 'all' ? 'display:none' : '' }}">
                            <td class="p-2.5 text-left sticky left-0 bg-[#1B2559] z-20 border-r border-[#2d3a6b] min-w-[130px] md:min-w-[200px]">
                                <div class="text-[9px] uppercase tracking-wider font-black text-blue-200">Total Hadir</div>
                            </td>
                            @foreach($dates as $date)
                                @php $s = $dailySummary[$tipeRow][$date] ?? ['bandongan'=>0,'bandongan_total'=>0,'wirid'=>0,'wirid_total'=>0,'yasinan'=>0,'yasinan_total'=>0]; @endphp
                                <td class="p-1.5 border-r border-[#2d3a6b] align-middle">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <div class="flex items-center gap-0.5">
                                            <span class="text-[8px] text-blue-300 font-bold">B</span>
                                            <span class="text-green-300 font-extrabold text-[10px]">{{ $s['bandongan'] }}/{{ $s['bandongan_total'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <span class="text-[8px] text-blue-300 font-bold">W</span>
                                            <span class="text-green-300 font-extrabold text-[10px]">{{ $s['wirid'] }}/{{ $s['wirid_total'] }}</span>
                                        </div>
                                        @if($tipeRow !== 'kepkam')
                                        <div class="flex items-center gap-0.5">
                                            <span class="text-[8px] text-blue-300 font-bold">Y</span>
                                            <span class="text-green-300 font-extrabold text-[10px]">{{ $s['yasinan'] }}/{{ $s['yasinan_total'] }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            {{-- Summary totals --}}
                            @php
                                $sumBH = array_sum(array_column($dailySummary[$tipeRow], 'bandongan'));
                                $sumBT = array_sum(array_column($dailySummary[$tipeRow], 'bandongan_total'));
                                $sumWH = array_sum(array_column($dailySummary[$tipeRow], 'wirid'));
                                $sumWT = array_sum(array_column($dailySummary[$tipeRow], 'wirid_total'));
                                $sumYH = array_sum(array_column($dailySummary[$tipeRow], 'yasinan'));
                                $sumYT = array_sum(array_column($dailySummary[$tipeRow], 'yasinan_total'));
                            @endphp
                            <td class="p-2 text-center border-r border-[#2d3a6b] bg-[#151d47]">
                                <span class="text-green-300 font-extrabold text-xs">{{ $sumBH }}/{{ $sumBT }}</span>
                            </td>
                            <td class="p-2 text-center border-r border-[#2d3a6b] bg-[#151d47]">
                                <span class="text-green-300 font-extrabold text-xs">{{ $sumWH }}/{{ $sumWT }}</span>
                            </td>
                            <td class="p-2 text-center bg-[#151d47]">
                                @if($tipeRow !== 'kepkam')
                                    <span class="text-green-300 font-extrabold text-xs">{{ $sumYH }}/{{ $sumYT }}</span>
                                @else
                                    <span class="text-gray-500 select-none">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </thead>
                    <tbody class="text-xs">
                        @php
                            $currentDivisi = null;
                        @endphp
                        @forelse($rekapData as $row)
                            @if($row['divisi'] !== $currentDivisi)
                                @php
                                    $currentDivisi = $row['divisi'];
                                @endphp
                                <tr class="bg-[#F8F9FD] font-bold text-[#1B2559] text-left table-divider-row" data-divisi="{{ $currentDivisi }}">
                                    <td colspan="{{ count($dates) + 4 }}" class="p-3 px-4 text-[10px] tracking-wider uppercase border border-gray-100 text-gray-500 font-bold bg-[#F8F9FD]">
                                        <div class="sticky left-4 inline-block">📁 Divisi: {{ $currentDivisi }}</div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-gray-50/50 border-b border-gray-100 last:border-0 transition-colors data-row" data-tipe="{{ $row['tipe'] }}" data-divisi="{{ $row['divisi'] }}">
                                <!-- Pengurus Info -->
                                <td class="p-3 text-left sticky left-0 bg-white z-20 border-r border-gray-100 shadow-[2px_0_5px_rgba(0,0,0,0.02)] w-[130px] md:w-[200px] min-w-[130px] md:min-w-[200px] max-w-[130px] md:max-w-[200px]">
                                    <div class="font-bold text-[#1B2559] text-xs truncate" title="{{ $row['nama'] }}">{{ $row['nama'] }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5 truncate" title="{{ $row['jabatan'] }}">{{ $row['jabatan'] }}</div>
                                </td>

                                <!-- Attendance Columns -->
                                @foreach($dates as $date)
                                    @php
                                        $att = $row['attendance'][$date];
                                        $statusBg = fn($s) => match($s) {
                                            'H' => 'bg-green-100 text-green-700 hover:bg-green-200',
                                            'S' => 'bg-amber-100 text-amber-700 hover:bg-amber-200',
                                            'I' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                                            'A' => 'bg-red-100 text-red-700 hover:bg-red-200',
                                            'L' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-gray-100 text-gray-500'
                                        };
                                        $statusLabel = fn($s) => match($s) {
                                            'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin',
                                            'A' => 'Alpa', 'L' => 'Libur', default => 'N/A'
                                        };
                                    @endphp
                                    <td class="p-2 border-r border-gray-100 align-middle {{ ($att['bandongan'] === 'L' || $att['wirid'] === 'L' || $att['yasinan'] === 'L') ? 'bg-purple-50/50' : '' }}">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- Bandongan Badge (B) -->
                                            @if($att['bandongan'])
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black cursor-help transition-all {{ $statusBg($att['bandongan']) }}"
                                                    title="Bandongan ({{ $date }}): {{ $statusLabel($att['bandongan']) }}">B</span>
                                            @else
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] text-gray-300 border border-dashed border-gray-200 select-none">-</span>
                                            @endif

                                            <!-- Wirid Badge (W) -->
                                            @if($att['wirid'])
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black cursor-help transition-all {{ $statusBg($att['wirid']) }}"
                                                    title="Wirid ({{ $date }}): {{ $statusLabel($att['wirid']) }}">W</span>
                                            @else
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] text-gray-300 border border-dashed border-gray-200 select-none">-</span>
                                            @endif

                                            <!-- Yasinan Badge (Y) -->
                                            @if($row['tipe'] !== 'kepkam')
                                                @if($att['yasinan'])
                                                    <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black cursor-help transition-all {{ $statusBg($att['yasinan']) }}"
                                                        title="Yasinan ({{ $date }}): {{ $statusLabel($att['yasinan']) }}">Y</span>
                                                @else
                                                    <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] text-gray-300 border border-dashed border-gray-200 select-none">-</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                <!-- Summary Columns -->
                                <!-- Bandongan Summary -->
                                <td class="p-3 text-center border-r border-gray-100 bg-[#F4F7FE]/30 font-bold text-[#1B2559]">
                                    <span class="text-green-600 font-extrabold">{{ $row['summary']['bandongan']['H'] }}</span>
                                    <span class="text-gray-300 text-[10px] font-normal mx-0.5">/</span>
                                    <span class="text-gray-500 text-[11px] font-medium">{{ $row['summary']['bandongan']['total'] }}</span>
                                </td>
                                <!-- Wirid Summary -->
                                <td class="p-3 text-center border-r border-gray-100 bg-[#F4F7FE]/30 font-bold text-[#1B2559]">
                                    <span class="text-green-600 font-extrabold">{{ $row['summary']['wirid']['H'] }}</span>
                                    <span class="text-gray-300 text-[10px] font-normal mx-0.5">/</span>
                                    <span class="text-gray-500 text-[11px] font-medium">{{ $row['summary']['wirid']['total'] }}</span>
                                </td>
                                <!-- Yasinan Summary -->
                                <td class="p-3 text-center bg-[#F4F7FE]/30 font-bold text-[#1B2559]">
                                    @if($row['tipe'] === 'kepkam')
                                        <span class="text-gray-300 select-none">-</span>
                                    @else
                                        <span class="text-green-600 font-extrabold">{{ $row['summary']['yasinan']['H'] }}</span>
                                        <span class="text-gray-300 text-[10px] font-normal mx-0.5">/</span>
                                        <span class="text-gray-500 text-[11px] font-medium">{{ $row['summary']['yasinan']['total'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($dates) + 4 }}" class="p-8 text-center text-[#A3AED0] italic">
                                    Belum ada data absensi pengurus
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Legend Section -->
            <div class="mt-6 pt-5 border-t border-gray-100 flex flex-wrap gap-4 items-center justify-between text-[11px]">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-gray-500 font-semibold uppercase tracking-wider text-[10px]">Legenda Status:</span>
                    <div class="flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 rounded-full font-medium">
                        <span class="w-3.5 h-3.5 rounded bg-green-200 text-green-800 text-[8px] font-black flex items-center justify-center">H</span> Hadir
                    </div>
                    <div class="flex items-center gap-1.5 bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full font-medium">
                        <span class="w-3.5 h-3.5 rounded bg-amber-200 text-amber-800 text-[8px] font-black flex items-center justify-center">S</span> Sakit
                    </div>
                    <div class="flex items-center gap-1.5 bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-medium">
                        <span class="w-3.5 h-3.5 rounded bg-blue-200 text-blue-800 text-[8px] font-black flex items-center justify-center">I</span> Izin
                    </div>
                    <div class="flex items-center gap-1.5 bg-red-50 text-red-700 px-2.5 py-1 rounded-full font-medium">
                        <span class="w-3.5 h-3.5 rounded bg-red-200 text-red-800 text-[8px] font-black flex items-center justify-center">A</span> Alpa
                    </div>
                    <div class="flex items-center gap-1.5 bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full font-medium">
                        <span class="w-3.5 h-3.5 rounded bg-purple-200 text-purple-800 text-[8px] font-black flex items-center justify-center">L</span> Libur
                    </div>
                </div>
                <div class="flex items-center gap-3 text-gray-400">
                    <span class="font-bold"><span class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded text-[9px] font-black">B</span> Bandongan</span>
                    <span class="font-bold"><span class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded text-[9px] font-black">W</span> Wirid</span>
                    <span class="font-bold"><span class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded text-[9px] font-black">Y</span> Yasinan</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadPdf() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const tipe = window.activeTipe || 'all';
            const url = `/mahadiyah/rekap-absensi-pengurus/download?start_date=${startDate}&end_date=${endDate}&tipe=${tipe}`;
            window.location.href = url;
        }

        function downloadExcel() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const tipe = window.activeTipe || 'all';
            const url = `/mahadiyah/rekap-absensi-pengurus/excel?start_date=${startDate}&end_date=${endDate}&tipe=${tipe}`;
            window.location.href = url;
        }

        // Track tipe aktif untuk download
        window.activeTipe = 'all';

        function filterTipe(tipe) {
            window.activeTipe = tipe;
            // Update active tab styling
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-[#4318FF]', 'text-white');
                btn.classList.add('bg-white', 'text-[#2B3674]', 'border', 'border-gray-200');
            });
            const activeBtn = document.getElementById('tab-' + tipe);
            activeBtn.classList.remove('bg-white', 'text-[#2B3674]', 'border', 'border-gray-200');
            activeBtn.classList.add('bg-[#4318FF]', 'text-white');

            // Show/hide data rows
            const rows = document.querySelectorAll('.data-row');
            rows.forEach(row => {
                row.style.display = (tipe === 'all' || row.getAttribute('data-tipe') === tipe) ? '' : 'none';
            });

            // Handle division divider rows
            document.querySelectorAll('.table-divider-row').forEach(div => {
                const divName = div.getAttribute('data-divisi');
                let hasVisible = false;
                document.querySelectorAll(`.data-row[data-divisi="${divName}"]`).forEach(sib => {
                    if (sib.style.display !== 'none') hasVisible = true;
                });
                div.style.display = hasVisible ? '' : 'none';
            });

            // Show/hide total hadir rows sesuai tipe aktif
            document.querySelectorAll('.total-hadir-row').forEach(row => {
                row.style.display = 'none';
            });
            const targetRow = document.querySelector('.total-hadir-' + tipe);
            if (targetRow) targetRow.style.display = '';
        }
    </script>
@endsection
