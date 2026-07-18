@extends('mahadiyah.layout')

@section('content')
    <div class="mt-4">
        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="text-xl md:text-2xl font-bold text-[#1B2559]">Rekap Absensi Kepala Kamar</h2>
            <p class="text-[#A3AED0] text-xs md:text-sm mt-1">Pantau keaktifan kepala kamar dalam mengabsen santri</p>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-4">
            <form method="GET" action="/mahadiyah/rekap-kegiatan"
                class="flex flex-col md:flex-row gap-3 md:gap-4 items-end">
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-bold text-[#1B2559] mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}" class="w-full bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-11 px-4 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all">
                </div>
                <div class="w-full md:flex-1">
                    <label class="block text-sm font-bold text-[#1B2559] mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}" class="w-full bg-[#F4F7FE] border-0 text-gray-600 text-sm rounded-xl h-11 px-4 focus:ring-2 focus:ring-[#4318FF] focus:bg-white outline-none transition-all">
                </div>
                <button type="submit"
                    class="btn bg-[#4318FF] text-white hover:bg-[#3311CC] px-6 w-full md:w-auto flex justify-center items-center">
                    <i class="fa fa-search mr-2"></i>Tampilkan
                </button>
            </form>
        </div>

        <!-- Download Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 w-full mb-6">
            <button onclick="downloadPdf()"
                class="btn bg-white border border-[#E0E5F2] text-[#2B3674] shadow-sm hover:bg-gray-50 flex items-center justify-center gap-2 w-full sm:w-auto">
                <i class="fa fa-file-pdf"></i>
                <span>Download PDF</span>
            </button>
        </div>

        <!-- Recap Table -->
        <div class="card">
            <div class="mb-3 md:mb-4 pb-3 md:pb-4 border-b-2 border-gray-300 text-center">
                <h2 class="text-base md:text-xl font-bold text-[#1B2559] mb-1 md:mb-2">Rekap Absensi Kepala Kamar</h2>
                <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-2 md:mb-1">Pondok Pesantren An-Nur II "Al-Murtadlo"</h3>
                <p class="text-xs md:text-sm text-[#2B3674]">
                    <span class="font-semibold">Periode:</span>
                    {{ $startDate->locale('id')->isoFormat('DD MMMM YYYY') }} - {{ $endDate->locale('id')->isoFormat('DD MMMM YYYY') }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse" style="text-align: center;">
                    <thead>
                        <tr class="bg-[#4318FF]">
                            <th rowspan="2" class="text-xs uppercase text-white font-bold p-3 border border-[#3311CC] sticky left-0 bg-[#4318FF] z-20 text-center min-w-[150px]">
                                Kepala Kamar
                            </th>
                            <th colspan="{{ count($dates) }}" class="text-xs uppercase text-white font-bold p-3 border border-[#3311CC] text-center">
                                Tanggal
                            </th>
                            <th rowspan="2" class="text-xs uppercase text-white font-bold p-3 border border-[#3311CC] text-center min-w-[80px]">
                                Total
                            </th>
                            <th rowspan="2" class="text-xs uppercase text-white font-bold p-3 border border-[#3311CC] text-center min-w-[100px]">
                                Persentase
                            </th>
                        </tr>
                        <tr class="bg-[#4318FF]">
                            @foreach($dates as $date)
                                @php
                                    $dateObj = \Carbon\Carbon::createFromFormat('d/m/Y', $date);
                                @endphp
                                <th class="text-xs text-white font-bold p-2 border border-[#3311CC] text-center min-w-[50px]">
                                    {{ $dateObj->format('d') }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($rekapData as $index => $kepkam)
                            <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors">
                                <td class="p-3 text-[#2B3674] text-sm font-medium sticky left-0 bg-white z-20 text-left border border-gray-200 shadow-sm">
                                    {{ $kepkam['nama'] }}
                                </td>
                                @foreach($dates as $date)
                                    <td class="p-2 border border-gray-200" style="text-align: center; vertical-align: middle;">
                                        @if($kepkam['daily_status'][$date])
                                            <span class="text-green-600 text-xl font-bold">✓</span>
                                        @else
                                            <span class="text-red-600 text-xl font-bold">✗</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="p-3 text-[#2B3674] text-sm font-bold border border-gray-200 text-center">
                                    {{ $kepkam['total'] }}
                                </td>
                                <td class="p-3 text-[#4318FF] text-sm font-bold border border-gray-200 text-center">
                                    {{ $kepkam['percentage'] }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($dates) + 3 }}" class="p-8 text-center text-[#A3AED0] italic">
                                    Belum ada data kepala kamar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
            function downloadPdf() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const url = `/mahadiyah/rekap-kegiatan/download?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = url;
            }
        </script>
@endsection