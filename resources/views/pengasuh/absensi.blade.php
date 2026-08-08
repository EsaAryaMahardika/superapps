@extends('pengasuh.layout')

@section('title', 'Rekap Absensi')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Rekap Absensi</h1>
@endsection

@section('content')
    <!-- Ringkasan Hari Ini & Santri Perhatian Khusus -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
        <!-- Perhatian Khusus -->
        <div class="card bg-red-50/50 border border-red-100">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-sm text-red-600 flex items-center gap-2">
                    <i class="fa fa-exclamation-triangle"></i> Santri Perhatian Khusus
                </h3>
                <a href="/pengasuh/track-record?type=santri" class="text-[10px] text-red-500 font-bold hover:underline">Lihat Profil</a>
            </div>
            <div class="space-y-2">
                @forelse($criticalSantri as $cs)
                    <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-red-100 shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <i class="fa fa-user-times text-red-500 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-[#1B2559] truncate">{{ $cs->nama }}</p>
                            <p class="text-xs text-[#A3AED0] mb-0.5"><i class="fa fa-user-tie text-xs mr-1"></i>Kepala Kamar: {{ $cs->kepkam }}</p>
                            <p class="text-[10px] text-red-500 font-medium">Alfa {{ $cs->total_alfa }}x bulan ini</p>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center text-xs text-[#A3AED0] bg-white rounded-xl border border-gray-100">Tidak ada santri kritis bulan ini.</div>
                @endforelse
            </div>
        </div>

        <!-- Rekap Absensi Hari Ini -->
        <div class="card">
            <h3 class="font-bold text-sm text-[#1B2559] mb-3">Rekap Absensi Hari Ini</h3>
            <div class="space-y-2">
                @foreach($activityRecap as $act)
                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                        <span class="text-xs font-medium text-[#2B3674]">{{ $act['name'] }}</span>
                        <div class="flex gap-1 text-[10px] font-bold">
                            <span class="bg-green-100 text-green-600 px-1.5 py-0.5 rounded">H:{{ $act['H'] }}</span>
                            <span class="bg-yellow-100 text-yellow-600 px-1.5 py-0.5 rounded">S:{{ $act['S'] }}</span>
                            <span class="bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">I:{{ $act['I'] }}</span>
                            <span class="bg-red-100 text-red-600 px-1.5 py-0.5 rounded">A:{{ $act['A'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filter Tanggal Global -->
    <div class="card mb-6 p-3 md:p-4">
        <form method="GET" action="/pengasuh/absensi">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#4318FF]/10 flex items-center justify-center"><i class="fa fa-calendar-alt text-[#4318FF] text-sm"></i></div>
                    <h3 class="font-bold text-sm text-[#1B2559]">Periode Rekapan</h3>
                </div>
                <div class="flex flex-col md:flex-row gap-2 items-center">
                    <select name="period" onchange="toggleCustomDate()"
                        class="w-full md:w-auto bg-[#F4F7FE] border-none text-gray-600 text-sm rounded-xl h-10 px-4 focus:ring-2 focus:ring-[#4318FF] focus:outline-none">
                        <option value="hari_ini" {{ request('period') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="minggu_ini" {{ request('period') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="bulan_ini" {{ request('period', 'bulan_ini') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="tahun_ini" {{ request('period') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
                    </select>
                    <div id="customDateContainer" class="{{ request('period') == 'custom' ? 'flex' : 'hidden' }} items-center gap-2 w-full md:w-auto">
                        <input type="date" name="start" value="{{ request('start') }}"
                            class="flex-1 md:w-auto bg-[#F4F7FE] border-none text-gray-600 text-sm rounded-xl h-10 px-3 focus:ring-2 focus:ring-[#4318FF] focus:outline-none">
                        <span class="text-gray-400 text-xs">-</span>
                        <input type="date" name="end" value="{{ request('end') }}"
                            class="flex-1 md:w-auto bg-[#F4F7FE] border-none text-gray-600 text-sm rounded-xl h-10 px-3 focus:ring-2 focus:ring-[#4318FF] focus:outline-none">
                    </div>
                    <input type="hidden" name="tab" value="{{ $activeTab }}" id="hidden-tab">
                    <button type="submit"
                        class="w-full md:w-auto bg-[#4318FF] text-white text-sm rounded-xl px-4 py-2 font-medium hover:bg-[#3311CC] transition-colors flex justify-center items-center gap-2 whitespace-nowrap">
                        <i class="fa fa-search"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Horizontal Scroll Tabs Kegiatan -->
    <div class="flex overflow-x-auto hide-scrollbar gap-2 mb-6 -mx-4 px-4 md:mx-0 md:px-0">
        @foreach($activities as $act)
        <a href="/pengasuh/absensi?tab={{ $act['id'] }}&period={{ request('period', 'bulan_ini') }}{{ request('start') ? '&start='.request('start') : '' }}{{ request('end') ? '&end='.request('end') : '' }}"
            class="tab-btn {{ $activeTab === $act['id'] ? 'active' : '' }}">
            {{ $act['name'] }}
        </a>
        @endforeach
    </div>

    <!-- Summary Cards -->
    <div class="flex overflow-x-auto snap-x hide-scrollbar gap-3 pb-2 mb-4 -mx-4 px-4 md:mx-0 md:px-0">
        <div class="card snap-center min-w-[80px] flex-1 text-center p-3 md:p-6">
            <p class="text-xl md:text-2xl font-bold text-green-500">{{ $sumH }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Hadir</p>
        </div>
        <div class="card snap-center min-w-[80px] flex-1 text-center p-3 md:p-6">
            <p class="text-xl md:text-2xl font-bold text-yellow-500">{{ $sumS }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Sakit</p>
        </div>
        <div class="card snap-center min-w-[80px] flex-1 text-center p-3 md:p-6">
            <p class="text-xl md:text-2xl font-bold text-blue-500">{{ $sumI }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Izin</p>
        </div>
        <div class="card snap-center min-w-[80px] flex-1 text-center p-3 md:p-6">
            <p class="text-xl md:text-2xl font-bold text-red-500">{{ $sumA }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Alfa</p>
        </div>
    </div>

    <!-- Tabel & Mobile List -->
    <div class="card p-0 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-sm md:text-lg text-[#1B2559]">Rekap per Kamar — {{ $actConfig['name'] }}</h3>
            <span class="text-xs text-[#A3AED0] font-medium hidden md:block">{{ $startFmt }} - {{ $endFmt }}</span>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Kamar</th>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Kepala Kamar</th>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">Total</th>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">H</th>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">S</th>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">I</th>
                        <th class="px-4 py-3 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">A</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapKamar as $rk)
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-[#2B3674]">{{ $rk['kamar'] }}</span>
                            <span class="block text-[10px] text-[#A3AED0]">{{ $rk['asrama'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#A3AED0]">{{ $rk['kepkam'] }}</td>
                        <td class="px-4 py-3 text-center text-sm font-bold text-[#2B3674]">{{ $rk['total'] }}</td>
                        <td class="px-4 py-3 text-center"><span class="bg-green-100 text-green-600 px-2 py-0.5 rounded text-xs font-bold">{{ $rk['H'] }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded text-xs font-bold">{{ $rk['S'] }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded text-xs font-bold">{{ $rk['I'] }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-xs font-bold">{{ $rk['A'] }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-[#A3AED0]">Tidak ada data absensi untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile List -->
        <div class="md:hidden flex flex-col divide-y divide-gray-100">
            @forelse($rekapKamar as $rk)
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-sm font-bold text-[#1B2559]">{{ $rk['kamar'] }}</p>
                        <p class="text-[10px] text-[#A3AED0]">{{ $rk['asrama'] }} &bull; {{ $rk['kepkam'] }}</p>
                    </div>
                    <span class="text-xs font-bold text-[#2B3674] bg-gray-100 px-2 py-0.5 rounded">{{ $rk['total'] }}</span>
                </div>
                <div class="flex gap-2 text-[10px] font-bold">
                    <span class="bg-green-100 text-green-600 px-2 py-1 rounded flex-1 text-center">H: {{ $rk['H'] }}</span>
                    <span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded flex-1 text-center">S: {{ $rk['S'] }}</span>
                    <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded flex-1 text-center">I: {{ $rk['I'] }}</span>
                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded flex-1 text-center">A: {{ $rk['A'] }}</span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-sm text-[#A3AED0]">Tidak ada data absensi untuk periode ini.</div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleCustomDate() {
    const val = document.querySelector('select[name="period"]').value;
    const container = document.getElementById('customDateContainer');
    if (val === 'custom') {
        container.classList.remove('hidden');
        container.classList.add('flex');
    } else {
        container.classList.add('hidden');
        container.classList.remove('flex');
    }
}
</script>
@endpush
