@extends('mahadiyah.layout')

@section('content')
    <div class="mt-2 sm:mt-4 px-1 sm:px-0">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-[#1B2559]">Dashboard Ringkasan Absensi</h2>
            </div>

            <div class="relative group max-w-xs md:max-w-none">
                <form id="summary-form" action="{{ url()->current() }}" method="GET">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <input type="hidden" name="rank_start_date" value="{{ $rankStartDate->format('Y-m-d') }}">
                    <input type="hidden" name="rank_end_date" value="{{ $rankEndDate->format('Y-m-d') }}">

                    <div
                        class="relative flex items-center gap-3 bg-white px-4 py-2.5 rounded-2xl shadow-sm border border-gray-100 cursor-pointer hover:shadow-md hover:border-indigo-100 transition-all duration-300">
                        <div
                            class="bg-[#F4F7FE] text-[#4318FF] w-10 h-10 flex items-center justify-center rounded-xl shadow-inner transition-colors">
                            <i class="fa-solid fa-calendar-day text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-[10px] text-[#A3AED0] font-bold uppercase tracking-wider leading-none mb-0.5">Ringkasan
                                Absensi</span>
                            <span class="text-sm font-bold text-[#1B2559] leading-tight">
                                {{ $summaryDate->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div class="ml-2 text-[#A3AED0]">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>

                        <!-- Invisible Date Input Overlay -->
                        <input type="date" name="summary_date" value="{{ $summaryDate->format('Y-m-d') }}"
                            onchange="this.form.submit()" onclick="try{this.showPicker()}catch(e){}"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" style="top:0; left:0;"
                            title="Pilih Tanggal">
                    </div>
                </form>
            </div>
        </div>

        <!-- Compact Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mb-6">

            <!-- KepKam Belum -->
            <div onclick="openDetailModal('KepKam Belum Absen', listKepkamBelum, 'red', 'xmark', false)"
                class="bg-white rounded-xl p-3 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-l-4 border-red-500 shadow-sm group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-500 group-hover:text-white transition-colors">
                        <i class="fa fa-xmark text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Belum Absen</p>
                        <h4 class="text-xl font-bold text-gray-800 leading-tight">{{ $kepkamBelumAbsen }} <span
                                class="text-sm font-medium text-gray-400">/ {{ $totalKepkam }}</span></h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">
                            {{ $totalKepkam > 0 ? round(($kepkamBelumAbsen / $totalKepkam) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Santri Hadir -->
            <div onclick="openDetailModal('Santri Hadir', listSantriHadir, 'blue', 'users')"
                class="bg-white rounded-xl p-3 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-l-4 border-blue-500 shadow-sm group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                        <i class="fa fa-users text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Hadir</p>
                        <h4 class="text-xl font-bold text-gray-800 leading-tight">{{ number_format($santriHadir) }} <span
                                class="text-sm font-medium text-gray-400">/ {{ number_format($totalSantri) }}</span></h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">
                            {{ $totalSantri > 0 ? round(($santriHadir / $totalSantri) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Santri Sakit -->
            <div onclick="openDetailModal('Santri Sakit', listSantriSakit, 'amber', 'bed')"
                class="bg-white rounded-xl p-3 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-l-4 border-amber-500 shadow-sm group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                        <i class="fa fa-bed text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Sakit</p>
                        <h4 class="text-xl font-bold text-gray-800 leading-tight">{{ number_format($santriSakit) }} <span
                                class="text-sm font-medium text-gray-400">/ {{ number_format($totalSantri) }}</span></h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">
                            {{ $totalSantri > 0 ? round(($santriSakit / $totalSantri) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Santri Izin -->
            <div onclick="openDetailModal('Santri Izin', listSantriIzin, 'orange', 'envelope-open-text')"
                class="bg-white rounded-xl p-3 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-l-4 border-orange-500 shadow-sm group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-colors">
                        <i class="fa fa-envelope-open-text text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Izin</p>
                        <h4 class="text-xl font-bold text-gray-800 leading-tight">{{ number_format($santriIzin) }} <span
                                class="text-sm font-medium text-gray-400">/ {{ number_format($totalSantri) }}</span></h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">
                            {{ $totalSantri > 0 ? round(($santriIzin / $totalSantri) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Santri Alpa -->
            <div onclick="openDetailModal('Santri Alpa', listSantriAlpa, 'rose', 'user-xmark')"
                class="bg-white rounded-xl p-3 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border-l-4 border-rose-500 shadow-sm group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 group-hover:bg-rose-500 group-hover:text-white transition-colors">
                        <i class="fa fa-user-xmark text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Alpa</p>
                        <h4 class="text-xl font-bold text-gray-800 leading-tight">{{ number_format($santriAlpa) }} <span
                                class="text-sm font-medium text-gray-400">/ {{ number_format($totalSantri) }}</span></h4>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">
                            {{ $totalSantri > 0 ? round(($santriAlpa / $totalSantri) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shared Detail Modal -->
        <div id="detail-modal"
            class="fixed inset-0 z-[9999] flex items-center justify-center hidden bg-black/40 backdrop-blur-[2px] opacity-0 transition-opacity duration-300"
            onclick="closeDetailModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 transform scale-95 opacity-0 transition-all duration-300"
                id="detail-modal-content" onclick="event.stopPropagation()">
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2.5 text-lg">
                        <span id="modal-icon-bg"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                            <i id="modal-icon" class="fa text-sm"></i>
                        </span>
                        <span id="modal-title">Detail</span>
                    </h3>
                    <button onclick="closeDetailModal()"
                        class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-0 max-h-[60vh] overflow-y-auto custom-scrollbar" id="modal-body">
                    <!-- Content injected via JS -->
                </div>
                <!-- Footer -->
                <div
                    class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex justify-between items-center">
                    <span class="text-xs text-gray-400" id="modal-footer-count"></span>
                    <button onclick="closeDetailModal()"
                        class="text-xs font-semibold text-gray-500 hover:text-gray-800 px-3 py-1.5 rounded-lg hover:bg-gray-200/50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm sm:text-lg font-bold text-[#1B2559]">Grafik Santri Hadir per Kegiatan</h3>
                    <p class="text-[10px] sm:text-xs text-[#A3AED0] mt-0.5 sm:mt-1">Tren 7 hari terakhir</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-[#F4F7FE] flex items-center justify-center">
                    <i class="fa fa-chart-line text-[#4318FF]"></i>
                </div>
            </div>
            <!-- Filter Buttons -->
            <!-- Filter & Legend -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
                <!-- Dataset Toggles -->
                <div class="flex flex-wrap gap-1.5" id="chart-filters">
                    <button type="button" onclick="toggleAllDatasets()" id="btn-filter-all"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 bg-[#4318FF] text-white shadow-sm hover:shadow-md">
                        <i class="fa fa-layer-group text-[10px]"></i> Semua
                    </button>
                </div>

                <!-- Date Range Filter -->
                <form id="chart-form" action="{{ url()->current() }}" method="GET"
                    class="w-full lg:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                    <input type="hidden" name="summary_date" value="{{ $summaryDate->format('Y-m-d') }}">
                    <input type="hidden" name="rank_start_date" value="{{ $rankStartDate->format('Y-m-d') }}">
                    <input type="hidden" name="rank_end_date" value="{{ $rankEndDate->format('Y-m-d') }}">

                    <!-- Unified Responsive Filter -->
                    <div
                        class="flex items-center bg-white p-1 rounded-2xl shadow-sm border border-gray-100 gap-2 flex-1 sm:flex-none">
                        <div
                            class="flex items-center bg-[#F4F7FE] rounded-xl px-2.5 py-1.5 hover:bg-gray-100 transition-colors gap-2 w-full">
                            <i class="fa-solid fa-calendar-day text-[#4318FF] text-[10px]"></i>
                            <div class="flex items-center gap-1">
                                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                                    onclick="try{this.showPicker()}catch(e){}"
                                    class="bg-transparent border-none p-0 text-[10px] sm:text-xs font-bold text-[#1B2559] focus:ring-0 cursor-pointer w-[70px] sm:w-28"
                                    title="Tgl Mulai">
                                <span class="text-[#A3AED0] font-bold text-[9px]">-</span>
                                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                                    onclick="try{this.showPicker()}catch(e){}"
                                    class="bg-transparent border-none p-0 text-[10px] sm:text-xs font-bold text-[#1B2559] focus:ring-0 cursor-pointer w-[70px] sm:w-28"
                                    title="Tgl Selesai">
                            </div>
                        </div>

                        <button type="submit"
                            class="bg-[#4318FF] hover:bg-indigo-700 text-white rounded-xl h-8 w-8 flex items-center justify-center transition-all shadow-md active:scale-95 flex-shrink-0"
                            title="Terapkan Filter">
                            <i class="fa fa-filter text-[10px]"></i>
                        </button>
                    </div>

                    <!-- Presets -->
                    <div class="flex items-center gap-1 justify-center sm:justify-start">
                        <button type="button" onclick="setDateRange(7)"
                            class="flex-1 sm:flex-none px-2.5 py-1.5 text-[10px] font-bold text-[#A3AED0] bg-white border border-gray-100 rounded-xl hover:bg-[#F4F7FE] hover:text-[#4318FF] hover:shadow-md transition-all">
                            1 Minggu
                        </button>
                        <button type="button" onclick="setDateRange(30)"
                            class="flex-1 sm:flex-none px-2.5 py-1.5 text-[10px] font-bold text-[#A3AED0] bg-white border border-gray-100 rounded-xl hover:bg-[#F4F7FE] hover:text-[#4318FF] hover:shadow-md transition-all">
                            1 Bulan
                        </button>
                    </div>
                </form>
            </div>
            <div class="-mx-4 sm:mx-0">
                <div class="relative w-full h-[300px] sm:h-[350px]">
                    <canvas id="chartKehadiran"></canvas>
                </div>
            </div>
        </div>

        <!-- Ranking Top 5 Problematic -->
        <div class="card hover:shadow-xl transition-all duration-300 mt-6 animate-fadeIn">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-sm sm:text-lg font-bold text-[#1B2559]">Top 5 Santri Paling Bermasalah</h3>
                    <p class="text-[10px] sm:text-xs text-[#A3AED0] mt-0.5 sm:mt-1">
                        Total Alpa terbanyak selama
                        <span class="font-semibold text-red-500">{{ $rankStartDate->format('d M') }} -
                            {{ $rankEndDate->format('d M') }}</span>
                    </p>
                </div>

                <!-- Ranking Date Filter -->
                <form id="ranking-form" action="{{ url()->current() }}" method="GET"
                    class="w-full lg:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                    <!-- Preserve chart dates and summary date -->
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <input type="hidden" name="summary_date" value="{{ $summaryDate->format('Y-m-d') }}">

                    <div
                        class="flex items-center bg-white p-1 rounded-2xl shadow-sm border border-gray-100 gap-2 flex-1 sm:flex-none">
                        <div
                            class="flex items-center bg-[#F4F7FE] rounded-xl px-2.5 py-1.5 hover:bg-gray-100 transition-colors gap-2 w-full">
                            <i class="fa-solid fa-calendar-check text-red-500 text-[10px]"></i>
                            <div class="flex items-center gap-1">
                                <input type="date" name="rank_start_date" value="{{ $rankStartDate->format('Y-m-d') }}"
                                    onclick="try{this.showPicker()}catch(e){}"
                                    class="bg-transparent border-none p-0 text-[10px] sm:text-xs font-bold text-[#1B2559] focus:ring-0 cursor-pointer w-[70px] sm:w-28"
                                    title="Tgl Mulai">
                                <span class="text-[#A3AED0] font-bold text-[9px]">-</span>
                                <input type="date" name="rank_end_date" value="{{ $rankEndDate->format('Y-m-d') }}"
                                    onclick="try{this.showPicker()}catch(e){}"
                                    class="bg-transparent border-none p-0 text-[10px] sm:text-xs font-bold text-[#1B2559] focus:ring-0 cursor-pointer w-[70px] sm:w-28"
                                    title="Tgl Selesai">
                            </div>
                        </div>

                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white rounded-xl h-8 w-8 flex items-center justify-center transition-all shadow-md active:scale-95 flex-shrink-0"
                            title="Terapkan Filter Ranking">
                            <i class="fa fa-filter text-[10px]"></i>
                        </button>
                    </div>

                    <!-- Ranking Presets -->
                    <div class="flex items-center gap-1 justify-center sm:justify-start">
                        <button type="button" onclick="setRankDateRange(7)"
                            class="flex-1 sm:flex-none px-2.5 py-1.5 text-[10px] font-bold text-[#A3AED0] bg-white border border-gray-100 rounded-xl hover:bg-red-50 hover:text-red-500 hover:shadow-md transition-all">
                            1 Minggu
                        </button>
                        <button type="button" onclick="setRankDateRange(30)"
                            class="flex-1 sm:flex-none px-2.5 py-1.5 text-[10px] font-bold text-[#A3AED0] bg-white border border-gray-100 rounded-xl hover:bg-red-50 hover:text-red-500 hover:shadow-md transition-all">
                            1 Bulan
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="py-3 px-2 text-[10px] sm:text-xs font-bold text-[#A3AED0] uppercase tracking-wider">
                                Santri</th>
                            <th
                                class="py-3 px-2 text-[10px] sm:text-xs font-bold text-[#A3AED0] uppercase tracking-wider hidden sm:table-cell">
                                Kepala Kamar</th>
                            <th
                                class="py-3 px-2 text-[10px] sm:text-xs font-bold text-[#A3AED0] uppercase tracking-wider text-right">
                                Frekuensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($topRanking as $rank)
                            <tr onclick="openDetailModal('Detail Alpa: {{ $rank['nama'] }}', '{{ json_encode($rank['details']) }}', 'red', 'user-xmark')"
                                class="hover:bg-red-50/50 cursor-pointer transition-colors group/row"
                                title="Klik untuk rincian kegiatan">
                                <td class="py-3 sm:py-4 px-2">
                                    <div class="flex flex-col">
                                        <p
                                            class="text-[11px] sm:text-sm font-bold text-[#1B2559] leading-tight mb-0.5 group-hover/row:text-red-600 transition-colors">
                                            {{ $rank['nama'] }}
                                        </p>
                                        <p class="text-[9px] sm:text-xs text-[#A3AED0] font-medium sm:hidden italic">
                                            Kepkam: {{ $rank['kepkam'] }}
                                        </p>
                                    </div>
                                </td>
                                <td class="py-3 sm:py-4 px-2 hidden sm:table-cell">
                                    <p class="text-xs text-[#A3AED0] font-medium">{{ $rank['kepkam'] }}</p>
                                </td>
                                <td class="py-3 sm:py-4 px-2 text-right">
                                    <div class="flex flex-col items-end">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold bg-red-50 text-red-600 border border-red-100 shadow-sm leading-none group-hover/row:bg-red-500 group-hover/row:text-white group-hover/row:border-red-500 transition-all">
                                            {{ $rank['count'] }} <span class="ml-1">Alpa Kegiatan</span>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-10 text-center">
                                    <i class="fa fa-shield-check text-[#05CD99] text-3xl mb-3 opacity-20"></i>
                                    <p class="text-xs text-[#A3AED0] font-bold">Luar biasa! Tidak ada data Alpa yang ditemukan.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="date"]::-webkit-clear-button {
            display: none !important;
            -webkit-appearance: none !important;
        }

        /* For other browsers */
        input[type="date"] {
            appearance: none;
            -moz-appearance: textfield;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn:not(.hidden) {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        const listKepkamSudah = @json($listKepkamSudah);
        const listKepkamBelum = @json($listKepkamBelum);
        const listSantriHadir = @json($listSantriHadirNama);
        const listSantriSakit = @json($listSantriSakitNama);
        const listSantriIzin = @json($listSantriIzinNama);
        const listSantriAlpa = @json($listSantriAlpaNama);

        let kehadiranChart = null;

        function setDateRange(days) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));

            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            const form = document.getElementById('chart-form');
            if (form) {
                form.querySelector('input[name="start_date"]').value = formatDate(start);
                form.querySelector('input[name="end_date"]').value = formatDate(end);
                form.submit();
            }
        }

        function setRankDateRange(days) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));

            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            const form = document.getElementById('ranking-form');
            if (form) {
                form.querySelector('input[name="rank_start_date"]').value = formatDate(start);
                form.querySelector('input[name="rank_end_date"]').value = formatDate(end);
                form.submit();
            }
        }

        function openDetailModal(title, dataStr, color, icon) {
            let data = [];
            try {
                // Handle both stringified JSON and direct objects if passed differently
                data = typeof dataStr === 'string' ? JSON.parse(dataStr) : dataStr;
            } catch (e) {
                console.error('Error parsing data', e);
                data = [];
            }

            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('detail-modal-content');
            const body = document.getElementById('modal-body');

            // Set Header
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-icon').className = `fa fa-${icon}`;

            // Dynamic colors - using classes that definitely exist in the HTML to avoid JIT purging
            // We used bg-{color}-50 and text-{color}-600 in the cards, so we reuse them here
            const iconBg = document.getElementById('modal-icon-bg');
            // Remove old color classes first (simple reset)
            iconBg.className = `w-8 h-8 rounded-lg flex items-center justify-center transition-colors bg-${color}-50 text-${color}-600`;

            // Calculate total count
            let totalCount = 0;
            const isGrouped = data.length > 0 && data[0].hasOwnProperty('kepkam');

            if (isGrouped) {
                data.forEach(group => totalCount += group.list.length);
            } else {
                totalCount = data.length;
            }

            document.getElementById('modal-footer-count').innerText = `Total: ${totalCount} Alpa`;

            // Clear previous content
            body.innerHTML = '';

            if (totalCount === 0) {
                body.innerHTML = `
                                                                                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                                                                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                                                                                    <i class="fa fa-folder-open text-gray-300 text-2xl"></i>
                                                                                                </div>
                                                                                                <p class="text-gray-400 text-sm">Tidak ada data untuk ditampilkan.</p>
                                                                                            </div>
                                                                                        `;
            } else {
                let html = '<div class="divide-y divide-gray-100">';

                // Helper to render an item
                const renderItemHtml = (item, index) => {
                    const num = index + 1;
                    if (typeof item === 'string') {
                        // Simple string item
                        return `
                                    <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                                        <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500 mt-0.5">${num}</span>
                                        <p class="text-sm font-semibold text-gray-700">${item}</p>
                                    </div>
                                `;
                    } else {
                        // Complex item with activities
                        let badges = '';
                        if (item.kegiatan && item.kegiatan.length > 0) {
                            item.kegiatan.forEach(k => {
                                badges += `
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-red-50 text-red-600 border border-red-100 shadow-sm">
                                            <i class="fa fa-square-xmark text-[10px]"></i>
                                            ${k}
                                        </span>
                                    `;
                            });
                        }
                        return `
                                <div class="group/item border-b border-gray-50 last:border-0">
                                    <div class="flex items-center justify-between px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500 mt-0.5">${num}</span>
                                            <p class="text-sm font-bold text-gray-800">${item.nama}</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50/30 px-5 pb-3 pt-0 ml-9 border-l-2 border-gray-100 mb-2">
                                        <div class="flex flex-wrap gap-2">
                                            ${badges}
                                        </div>
                                    </div>
                                </div>
                            `;
                    }
                };

                if (isGrouped) {
                    let globalIndex = 0;
                    data.forEach(group => {
                        // Group Header
                        html += `
                                    <div class="bg-gray-50/90 backdrop-blur-sm border-b border-gray-100 px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider sticky top-0 z-10 shadow-sm">
                                        <i class="fa fa-user-tie mr-1.5 opacity-50"></i>${group.kepkam}
                                        <span class="ml-1 px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] text-gray-600 shadow-sm">${group.list.length}</span>
                                    </div>
                                `;
                        // Group Items
                        group.list.forEach(item => {
                            html += renderItemHtml(item, globalIndex++);
                        });
                    });
                } else {
                    // Flat List
                    data.forEach((item, index) => {
                        html += renderItemHtml(item, index);
                    });
                }

                html += '</div>';
                body.innerHTML = html;
            }

            // Show Modal
            modal.classList.remove('hidden');
            // Small delay for animation
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDetailModal() {
            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('detail-modal-content');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            modal.classList.add('opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function toggleDataset(index) {
            if (!kehadiranChart) return;
            const meta = kehadiranChart.getDatasetMeta(index);
            meta.hidden = !meta.hidden;
            kehadiranChart.update();
            updateFilterButtonState(index, !meta.hidden);
            updateSemuaButton();
        }

        function toggleAllDatasets() {
            if (!kehadiranChart) return;
            // If all are visible, hide all. Otherwise, show all.
            const anyHidden = kehadiranChart.data.datasets.some((_, i) => kehadiranChart.getDatasetMeta(i).hidden);
            kehadiranChart.data.datasets.forEach((_, i) => {
                kehadiranChart.getDatasetMeta(i).hidden = !anyHidden ? true : false;
            });
            kehadiranChart.update();

            // Update all buttons
            kehadiranChart.data.datasets.forEach((_, i) => {
                updateFilterButtonState(i, !kehadiranChart.getDatasetMeta(i).hidden);
            });
            updateSemuaButton();
        }

        function updateFilterButtonState(index, isActive) {
            const btn = document.getElementById('btn-filter-' + index);
            if (!btn) return;
            const color = btn.dataset.color;
            if (isActive) {
                btn.style.backgroundColor = color;
                btn.style.color = '#ffffff';
                btn.style.borderColor = color;
            } else {
                btn.style.backgroundColor = '#F4F7FE';
                btn.style.color = '#A3AED0';
                btn.style.borderColor = '#E9EDF7';
            }
        }

        function updateSemuaButton() {
            const btn = document.getElementById('btn-filter-all');
            const allShown = kehadiranChart.data.datasets.every((_, i) => !kehadiranChart.getDatasetMeta(i).hidden);
            if (allShown) {
                btn.classList.add('bg-[#4318FF]', 'text-white');
                btn.classList.remove('bg-[#F4F7FE]', 'text-[#A3AED0]');
            } else {
                btn.classList.remove('bg-[#4318FF]', 'text-white');
                btn.classList.add('bg-[#F4F7FE]', 'text-[#A3AED0]');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartKehadiran').getContext('2d');

            const labels = @json($chartLabelsFormatted);
            const datasets = @json($chartDatasets);

            const colorPalette = [
                { border: '#4318FF', bg: 'rgba(67, 24, 255, 0.08)' },
                { border: '#05CD99', bg: 'rgba(5, 205, 153, 0.08)' },
                { border: '#FFB547', bg: 'rgba(255, 181, 71, 0.08)' },
                { border: '#EE5D50', bg: 'rgba(238, 93, 80, 0.08)' },
                { border: '#868CFF', bg: 'rgba(134, 140, 255, 0.08)' },
                { border: '#01B574', bg: 'rgba(1, 181, 116, 0.08)' },
                { border: '#3965FF', bg: 'rgba(57, 101, 255, 0.08)' },
                { border: '#E31A1A', bg: 'rgba(227, 26, 26, 0.08)' },
            ];

            const chartDatasets = datasets.map((ds, i) => ({
                label: ds.label,
                data: ds.data,
                borderColor: colorPalette[i % colorPalette.length].border,
                backgroundColor: colorPalette[i % colorPalette.length].bg,
                borderWidth: 2.5,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: colorPalette[i % colorPalette.length].border,
                pointBorderWidth: 2.5,
                pointHoverBackgroundColor: colorPalette[i % colorPalette.length].border,
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 2,
                tension: 0.4,
                fill: false,
                datalabels: {
                    color: colorPalette[i % colorPalette.length].border,
                },
            }));

            // Generate filter buttons dynamically
            const filterContainer = document.getElementById('chart-filters');
            datasets.forEach((ds, i) => {
                const color = colorPalette[i % colorPalette.length].border;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.id = 'btn-filter-' + i;
                btn.dataset.color = color;
                btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 border shadow-sm hover:shadow-md';
                btn.style.backgroundColor = color;
                btn.style.color = '#ffffff';
                btn.style.borderColor = color;
                btn.innerHTML = '<span class="w-2 h-2 rounded-full bg-white/50 flex-shrink-0"></span>' + ds.label;
                btn.onclick = () => toggleDataset(i);
                filterContainer.appendChild(btn);
            });

            Chart.register(ChartDataLabels);

            kehadiranChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: chartDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    clip: false,
                    layout: {
                        padding: { top: 30, right: 15 }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        datalabels: {
                            display: function (context) {
                                return window.innerWidth > 640 && context.dataset.data[context.dataIndex] > 0;
                            },
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            font: {
                                family: "'DM Sans', sans-serif",
                                size: 10,
                                weight: 700
                            },
                            backgroundColor: 'rgba(255,255,255,0.85)',
                            borderRadius: 3,
                            padding: { top: 1, bottom: 1, left: 3, right: 3 },
                            formatter: function (value) {
                                return value;
                            }
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1B2559',
                            titleColor: '#ffffff',
                            bodyColor: '#E9EDF7',
                            titleFont: {
                                family: "'DM Sans', sans-serif",
                                size: 13,
                                weight: 700
                            },
                            bodyFont: {
                                family: "'DM Sans', sans-serif",
                                size: 12,
                            },
                            padding: 14,
                            cornerRadius: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' santri';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: {
                                    family: "'DM Sans', sans-serif",
                                    size: window.innerWidth > 640 ? 12 : 10,
                                    weight: 500
                                },
                                color: '#A3AED0',
                                maxTicksLimit: window.innerWidth > 640 ? 10 : 7,
                                autoSkip: true,
                                maxRotation: 0
                            },
                            border: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(163, 174, 208, 0.1)' },
                            ticks: {
                                font: { family: "'DM Sans', sans-serif", size: 12, weight: 500 },
                                color: '#A3AED0',
                                stepSize: 1
                            },
                            border: { display: false }
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        });
    </script>
@endsection