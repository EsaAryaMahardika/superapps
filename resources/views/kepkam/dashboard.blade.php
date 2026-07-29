@extends('kepkam.layout')

@section('content')
    <div class="mt-4">
        <h2 class="text-2xl font-bold mb-6 text-[#1B2559]">Dashboard Absensi Santri</h2>

        {{-- ===== SECTION 1: STAT CARDS ===== --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            {{-- Hadir --}}
            <div class="card h-fit border-l-4 border-[#05CD99]">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Hadir</span>
                    <div class="w-8 h-8 rounded-lg bg-[#05CD99]/10 flex items-center justify-center">
                        <i class="fa fa-check text-[#05CD99] text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-[#1B2559]">{{ $stats['hadir'] }}</p>
                <div class="flex items-center gap-1 mt-1">
                    @if($stats['trend'] > 0)
                        <span class="text-xs font-bold text-[#05CD99]"><i class="fa fa-arrow-up"></i> +{{ $stats['trend'] }}%</span>
                    @elseif($stats['trend'] < 0)
                        <span class="text-xs font-bold text-[#EE5D50]"><i class="fa fa-arrow-down"></i> {{ $stats['trend'] }}%</span>
                    @else
                        <span class="text-xs font-bold text-[#A3AED0]"><i class="fa fa-minus"></i> 0%</span>
                    @endif
                    <span class="text-[10px] text-[#A3AED0]">vs kemarin</span>
                </div>
            </div>

            {{-- Sakit --}}
            <div class="card h-fit border-l-4 border-[#FDB833] cursor-pointer hover:shadow-lg transition-all" onclick="showDetail('sakit')">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Sakit</span>
                    <div class="w-8 h-8 rounded-lg bg-[#FDB833]/10 flex items-center justify-center">
                        <i class="fa fa-thermometer-half text-[#FDB833] text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-[#1B2559]">{{ $stats['sakit'] }} <span class="text-sm font-normal text-[#A3AED0]">santri</span></p>
                <p class="text-[10px] text-[#4318FF] mt-1 font-bold"><i class="fa fa-mouse-pointer mr-1"></i>Klik untuk detail</p>
            </div>

            {{-- Izin --}}
            <div class="card h-fit border-l-4 border-[#4318FF] cursor-pointer hover:shadow-lg transition-all" onclick="showDetail('izin')">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Izin</span>
                    <div class="w-8 h-8 rounded-lg bg-[#4318FF]/10 flex items-center justify-center">
                        <i class="fa fa-hand-paper text-[#4318FF] text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-[#1B2559]">{{ $stats['izin'] }} <span class="text-sm font-normal text-[#A3AED0]">santri</span></p>
                <p class="text-[10px] text-[#4318FF] mt-1 font-bold"><i class="fa fa-mouse-pointer mr-1"></i>Klik untuk detail</p>
            </div>

            {{-- Alfa --}}
            <div class="card h-fit border-l-4 border-[#EE5D50] cursor-pointer hover:shadow-lg transition-all" onclick="showDetail('alfa')">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Alfa</span>
                    <div class="w-8 h-8 rounded-lg bg-[#EE5D50]/10 flex items-center justify-center">
                        <i class="fa fa-times text-[#EE5D50] text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-[#1B2559]">{{ $stats['alfa'] }} <span class="text-sm font-normal text-[#A3AED0]">santri</span></p>
                <p class="text-[10px] text-[#4318FF] mt-1 font-bold"><i class="fa fa-mouse-pointer mr-1"></i>Klik untuk detail</p>
            </div>
        </div>

        {{-- ===== DETAIL MODAL ===== --}}
        <div class="fixed inset-0 z-50 hidden items-center justify-center p-4" id="detailModal">
            <div class="absolute inset-0 bg-black/40" onclick="closeDetail()"></div>
            <div class="relative bg-white rounded-[20px] shadow-2xl w-full max-w-lg z-10 max-h-[80vh] flex flex-col">
                <div class="border-b border-gray-100 p-6 flex items-center justify-between">
                    <h5 class="text-xl font-bold text-[#1B2559]" id="detailTitle">Detail</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center" onclick="closeDetail()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto custom-scrollbar" id="detailBody">
                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: STACKED BAR CHART ===== --}}
        <div class="card mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3">
                <h3 class="text-lg font-bold text-[#1B2559]">Tren Kehadiran (7 Hari)</h3>
                <div class="flex flex-wrap gap-1.5" id="chartTabs">
                    <button type="button" onclick="switchChart('all')"
                        class="chart-tab active text-xs font-bold px-3 py-1.5 rounded-lg transition-all bg-[#4318FF] text-white">
                        Semua
                    </button>
                    @foreach($activities as $act)
                        <button type="button" onclick="switchChart('{{ $act['id'] }}')"
                            class="chart-tab text-xs font-bold px-3 py-1.5 rounded-lg transition-all bg-gray-100 text-[#A3AED0] hover:bg-gray-200">
                            {{ $act['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="attendanceChart"></canvas>
            </div>
            <div class="flex items-center justify-center gap-4 mt-4">
                <span class="flex items-center gap-1.5 text-xs text-[#A3AED0]"><span class="w-3 h-3 rounded-full bg-[#05CD99] inline-block"></span> Hadir</span>
                <span class="flex items-center gap-1.5 text-xs text-[#A3AED0]"><span class="w-3 h-3 rounded-full bg-[#FDB833] inline-block"></span> Sakit</span>
                <span class="flex items-center gap-1.5 text-xs text-[#A3AED0]"><span class="w-3 h-3 rounded-full bg-[#4318FF] inline-block"></span> Izin</span>
                <span class="flex items-center gap-1.5 text-xs text-[#A3AED0]"><span class="w-3 h-3 rounded-full bg-[#EE5D50] inline-block"></span> Alfa</span>
            </div>
        </div>

        {{-- ===== SECTION 3: HEATMAP TABLE ===== --}}
        <div class="card">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3">
                <h3 class="text-lg font-bold text-[#1B2559]">Heatmap Kehadiran Santri</h3>
                <div class="relative">
                    <select id="heatmapSelect" onchange="renderHeatmap(this.value)"
                        class="appearance-none bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-10 pl-4 pr-10 focus:ring-2 focus:ring-[#4318FF] outline-none transition-all">
                        @foreach($activities as $act)
                            <option value="{{ $act['id'] }}">{{ $act['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-[#A3AED0]">
                        <i class="fa fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="heatmapTable">
                    <thead>
                        <tr>
                            <th class="text-left text-xs uppercase text-[#A3AED0] font-bold p-3 sticky left-0 bg-white z-10 min-w-[140px]">Nama Santri</th>
                            @foreach($dates as $d)
                                <th class="text-center text-[10px] uppercase text-[#A3AED0] font-bold p-2 min-w-[52px]">
                                    {{ \Carbon\Carbon::createFromFormat('d/m/Y', $d)->locale('id')->isoFormat('ddd<br>DD/MM') }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="heatmapBody">
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100">
                <span class="text-[10px] text-[#A3AED0] font-bold uppercase">Keterangan:</span>
                <span class="flex items-center gap-1 text-xs text-[#A3AED0]"><span class="w-4 h-4 rounded bg-[#05CD99] inline-block"></span> H</span>
                <span class="flex items-center gap-1 text-xs text-[#A3AED0]"><span class="w-4 h-4 rounded bg-[#FDB833] inline-block"></span> S</span>
                <span class="flex items-center gap-1 text-xs text-[#A3AED0]"><span class="w-4 h-4 rounded bg-[#4318FF] inline-block"></span> I</span>
                <span class="flex items-center gap-1 text-xs text-[#A3AED0]"><span class="w-4 h-4 rounded bg-[#EE5D50] inline-block"></span> A</span>
                <span class="flex items-center gap-1 text-xs text-[#A3AED0]"><span class="w-4 h-4 rounded bg-gray-100 inline-block"></span> Belum</span>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        // === DATA ===
        const chartLabels = @json($chartLabels);
        const chartData = @json($chartData);
        const heatmapData = @json($heatmap);
        const santriList = @json($santriList);
        const dates = @json($dates->values());

        const detailData = {
            sakit: @json(array_values($detailSakit)),
            izin: @json(array_values($detailIzin)),
            alfa: @json(array_values($detailAlfa)),
        };
        const detailTitles = {
            sakit: 'Santri Sakit Hari Ini',
            izin: 'Santri Izin Hari Ini',
            alfa: 'Santri Alfa Hari Ini',
        };
        const detailColors = {
            sakit: '#FDB833',
            izin: '#4318FF',
            alfa: '#EE5D50',
        };

        // === DETAIL MODAL ===
        function showDetail(type) {
            const items = detailData[type] || [];
            const title = detailTitles[type];
            const color = detailColors[type];

            document.getElementById('detailTitle').textContent = title;

            let html = '';
            if (items.length === 0) {
                html = `<div class="text-center py-8">
                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fa fa-check text-xl text-[#05CD99]"></i>
                    </div>
                    <p class="text-sm text-[#A3AED0]">Tidak ada santri ${type} hari ini</p>
                </div>`;
            } else {
                html = `<div class="space-y-3">`;
                items.forEach((item, idx) => {
                    html += `<div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0" style="background:${color}">${idx + 1}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-[#1B2559]">${item.nama}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                ${item.kegiatan.map(k => `<span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold" style="background:${color}15;color:${color}">${k}</span>`).join('')}
                            </div>
                        </div>
                    </div>`;
                });
                html += `</div>`;
            }

            document.getElementById('detailBody').innerHTML = html;
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }

        // === CHART ===
        let currentChart = 'all';
        let attendanceChart;

        function getChartData(activityId) {
            if (activityId === 'all') {
                // Sum all activities per date
                const summed = dates.map((_, idx) => {
                    let h = 0, s = 0, i = 0, a = 0;
                    Object.values(chartData).forEach(actData => {
                        h += actData[idx]['H'];
                        s += actData[idx]['S'];
                        i += actData[idx]['I'];
                        a += actData[idx]['A'];
                    });
                    return { H: h, S: s, I: i, A: a };
                });
                return summed;
            }
            return chartData[activityId] || dates.map(() => ({ H: 0, S: 0, I: 0, A: 0 }));
        }

        function createChart(activityId) {
            const data = getChartData(activityId);
            const ctx = document.getElementById('attendanceChart').getContext('2d');

            if (attendanceChart) attendanceChart.destroy();

            attendanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: data.map(d => d.H),
                            backgroundColor: '#05CD99',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Sakit',
                            data: data.map(d => d.S),
                            backgroundColor: '#FDB833',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Izin',
                            data: data.map(d => d.I),
                            backgroundColor: '#4318FF',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Alfa',
                            data: data.map(d => d.A),
                            backgroundColor: '#EE5D50',
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111C44',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 12,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#A3AED0' }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: '#F4F7FE' },
                            ticks: { font: { size: 10 }, color: '#A3AED0', stepSize: 5 }
                        }
                    }
                }
            });
        }

        function switchChart(activityId) {
            currentChart = activityId;
            createChart(activityId);

            // Update tab styles
            document.querySelectorAll('.chart-tab').forEach(tab => {
                tab.classList.remove('bg-[#4318FF]', 'text-white');
                tab.classList.add('bg-gray-100', 'text-[#A3AED0]');
            });
            event.target.classList.remove('bg-gray-100', 'text-[#A3AED0]');
            event.target.classList.add('bg-[#4318FF]', 'text-white');
        }

        // === HEATMAP ===
        const statusColors = {
            'H': 'bg-[#05CD99] text-white',
            'S': 'bg-[#FDB833] text-white',
            'I': 'bg-[#4318FF] text-white',
            'A': 'bg-[#EE5D50] text-white',
        };

        function renderHeatmap(activityId) {
            const tbody = document.getElementById('heatmapBody');
            const actData = heatmapData[activityId] || {};

            let html = '';
            santriList.forEach(santri => {
                html += `<tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">`;
                html += `<td class="p-3 text-sm font-medium text-[#2B3674] sticky left-0 bg-white z-10">${santri.nama}</td>`;

                dates.forEach(date => {
                    const status = actData[santri.nis]?.[date] || null;
                    if (status) {
                        html += `<td class="p-1.5 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[10px] font-bold ${statusColors[status] || 'bg-gray-100 text-gray-400'}">${status}</span>
                        </td>`;
                    } else {
                        html += `<td class="p-1.5 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-300">-</span>
                        </td>`;
                    }
                });

                html += `</tr>`;
            });

            tbody.innerHTML = html;
        }

        // === INIT ===
        document.addEventListener('DOMContentLoaded', function () {
            createChart('all');
            renderHeatmap('subuh');
        });
    </script>
@endsection
