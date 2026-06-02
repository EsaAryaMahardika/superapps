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
                        value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}" class="form-control w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-0">
                </div>
                <div class="w-full md:flex-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1B2559] mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}" class="form-control w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-0">
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
                <button onclick="downloadCsv()"
                    class="btn bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] shadow-sm rounded-xl font-semibold py-2.5 px-4 flex items-center justify-center gap-2 text-xs flex-1 lg:flex-none">
                    <i class="fa fa-file-csv text-green-500"></i>
                    <span>Download CSV</span>
                </button>
                <button id="download-png-btn" onclick="downloadAsPng()"
                    class="btn bg-white border border-gray-200 hover:bg-gray-50 text-[#2B3674] shadow-sm rounded-xl font-semibold py-2.5 px-4 flex items-center justify-center gap-2 text-xs flex-1 lg:flex-none">
                    <i class="fa fa-image text-blue-500"></i>
                    <span>Download PNG</span>
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
                                    @endphp
                                    <td class="p-2 border-r border-gray-100 align-middle">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- Bandongan Badge (B) -->
                                            @if($att['bandongan'])
                                                @php
                                                    $bg = match($att['bandongan']) {
                                                        'H' => 'bg-green-100 text-green-700 hover:bg-green-200',
                                                        'S' => 'bg-amber-100 text-amber-700 hover:bg-amber-200',
                                                        'I' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                                                        'A' => 'bg-red-100 text-red-700 hover:bg-red-200',
                                                        default => 'bg-gray-100 text-gray-500'
                                                    };
                                                    $statusFull = match($att['bandongan']) {
                                                        'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', default => 'N/A'
                                                    };
                                                @endphp
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black cursor-help transition-all {{ $bg }}"
                                                    title="Bandongan ({{ $date }}): {{ $statusFull }}">B</span>
                                            @else
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] text-gray-300 border border-dashed border-gray-200 select-none">-</span>
                                            @endif

                                            <!-- Wirid Badge (W) -->
                                            @if($att['wirid'])
                                                @php
                                                    $bg = match($att['wirid']) {
                                                        'H' => 'bg-green-100 text-green-700 hover:bg-green-200',
                                                        'S' => 'bg-amber-100 text-amber-700 hover:bg-amber-200',
                                                        'I' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                                                        'A' => 'bg-red-100 text-red-700 hover:bg-red-200',
                                                        default => 'bg-gray-100 text-gray-500'
                                                    };
                                                    $statusFull = match($att['wirid']) {
                                                        'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', default => 'N/A'
                                                    };
                                                @endphp
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black cursor-help transition-all {{ $bg }}"
                                                    title="Wirid ({{ $date }}): {{ $statusFull }}">W</span>
                                            @else
                                                <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] text-gray-300 border border-dashed border-gray-200 select-none">-</span>
                                            @endif

                                            <!-- Yasinan Badge (Y) -->
                                            @if($row['tipe'] !== 'kepkam')
                                                @if($att['yasinan'])
                                                    @php
                                                        $bg = match($att['yasinan']) {
                                                            'H' => 'bg-green-100 text-green-700 hover:bg-green-200',
                                                            'S' => 'bg-amber-100 text-amber-700 hover:bg-amber-200',
                                                            'I' => 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                                                            'A' => 'bg-red-100 text-red-700 hover:bg-red-200',
                                                            default => 'bg-gray-100 text-gray-500'
                                                        };
                                                        $statusFull = match($att['yasinan']) {
                                                            'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', default => 'N/A'
                                                        };
                                                    @endphp
                                                    <span class="w-4 h-4 rounded flex items-center justify-center text-[9px] font-black cursor-help transition-all {{ $bg }}"
                                                        title="Yasinan ({{ $date }}): {{ $statusFull }}">Y</span>
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
                </div>
                <div class="flex items-center gap-3 text-gray-400">
                    <span class="font-bold"><span class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded text-[9px] font-black">B</span> Bandongan</span>
                    <span class="font-bold"><span class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded text-[9px] font-black">W</span> Wirid</span>
                    <span class="font-bold"><span class="bg-gray-100 text-gray-700 px-1 py-0.5 rounded text-[9px] font-black">Y</span> Yasinan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        async function renderPdfToCanvas() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const url = `/mahadiyah/rekap-absensi-pengurus/download?start_date=${startDate}&end_date=${endDate}`;
            
            console.log('[PDF Render] Starting PDF fetch from:', url);
            
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('pdf')) {
                    throw new Error(`Server tidak mengembalikan PDF (Content-Type: ${contentType})`);
                }
                
                const blob = await response.blob();
                const arrayBuffer = await blob.arrayBuffer();
                
                const loadingTask = pdfjsLib.getDocument({data: arrayBuffer});
                const pdf = await loadingTask.promise;

                const page = await pdf.getPage(1);
                const viewport = page.getViewport({ scale: 4 });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                
                return { canvas, startDate, endDate };
            } catch (error) {
                console.error('[PDF Render] Error:', error);
                throw error;
            }
        }

        async function downloadAsPng() {
            const btn = document.getElementById('download-png-btn');
            const originalContent = btn.innerHTML;

            try {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Processing...</span>';

                const { canvas, startDate, endDate } = await renderPdfToCanvas();

                const link = document.createElement('a');
                link.download = `Rekap_Absensi_Pengurus_${startDate}_to_${endDate}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();

            } catch (error) {
                alert('Gagal mendownload gambar: ' + (error.message || error));
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }

        function downloadPdf() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const url = `/mahadiyah/rekap-absensi-pengurus/download?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        }

        function downloadCsv() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const url = `/mahadiyah/rekap-absensi-pengurus/csv?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        }

        function filterTipe(tipe) {
            // Update active tab styling
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-[#4318FF]', 'text-white');
                btn.classList.add('bg-white', 'text-[#2B3674]', 'border', 'border-gray-200');
            });
            const activeBtn = document.getElementById('tab-' + tipe);
            activeBtn.classList.remove('bg-white', 'text-[#2B3674]', 'border', 'border-gray-200');
            activeBtn.classList.add('bg-[#4318FF]', 'text-white');

            // Show/hide rows
            const rows = document.querySelectorAll('.data-row');
            rows.forEach(row => {
                if (tipe === 'all' || row.getAttribute('data-tipe') === tipe) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle division divider rows if all rows under them are hidden
            const dividers = document.querySelectorAll('.table-divider-row');
            dividers.forEach(div => {
                const divName = div.getAttribute('data-divisi');
                let hasVisible = false;
                
                // Find all siblings that have the same division attribute and are visible
                const sibs = document.querySelectorAll(`.data-row[data-divisi="${divName}"]`);
                sibs.forEach(sib => {
                    if (sib.style.display !== 'none') {
                        hasVisible = true;
                    }
                });
                
                div.style.display = hasVisible ? '' : 'none';
            });
        }
    </script>
@endsection
