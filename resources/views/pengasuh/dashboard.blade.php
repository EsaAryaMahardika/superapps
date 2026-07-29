@extends('pengasuh.layout')

@section('title', 'Dashboard Pengasuh')
@section('header')
    <p class="text-[10px] md:text-sm text-[#A3AED0] font-medium">Selamat Datang</p>
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Pengasuh</h1>
@endsection

@section('content')
    <!-- Horizontal Scroll Summary Cards -->
    <div class="flex overflow-x-auto snap-x hide-scrollbar gap-4 pb-2 mb-4 -mx-4 px-4 md:mx-0 md:px-0">
        <div class="card snap-center min-w-[140px] flex-1">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-[#4318FF]/10 flex items-center justify-center"><i class="fa fa-users text-[#4318FF] text-sm"></i></div>
            </div>
            <p class="text-xl font-bold text-[#1B2559]">{{ $totalSantri }}</p>
            <p class="text-[10px] text-[#A3AED0] mt-0.5">Total Santri</p>
        </div>
        <div class="card snap-center min-w-[140px] flex-1">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center"><i class="fa fa-check-circle text-green-500 text-sm"></i></div>
            </div>
            <p class="text-xl font-bold text-[#1B2559]">{{ $todayStats['H'] }}</p>
            <p class="text-[10px] text-[#A3AED0] mt-0.5">Hadir Hari Ini</p>
        </div>
        <div class="card snap-center min-w-[140px] flex-1">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center"><i class="fa fa-hand-paper text-orange-500 text-sm"></i></div>
            </div>
            <p class="text-xl font-bold text-[#1B2559]">{{ $izinAktif }}</p>
            <p class="text-[10px] text-[#A3AED0] mt-0.5">Izin Aktif</p>
        </div>
        <div class="card snap-center min-w-[140px] flex-1">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center"><i class="fa fa-times-circle text-red-500 text-sm"></i></div>
            </div>
            <p class="text-xl font-bold text-[#1B2559]">{{ $todayStats['A'] }}</p>
            <p class="text-[10px] text-[#A3AED0] mt-0.5">Alfa Hari Ini</p>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="flex overflow-x-auto hide-scrollbar gap-2 mb-4 -mx-4 px-4 md:mx-0 md:px-0">
        <button onclick="switchTab('absensi')" id="btn-absensi" class="tab-btn active">Absensi & Kritis</button>
        <button onclick="switchTab('keuangan')" id="btn-keuangan" class="tab-btn">Keuangan</button>
        <button onclick="switchTab('perizinan')" id="btn-perizinan" class="tab-btn">Perizinan</button>
        <button onclick="switchTab('kesehatan')" id="btn-kesehatan" class="tab-btn">Kesehatan</button>
        <button onclick="switchTab('infrastruktur')" id="btn-infrastruktur" class="tab-btn">Infrastruktur</button>
        <button onclick="switchTab('logistik')" id="btn-logistik" class="tab-btn">Logistik</button>
    </div>

    <!-- TAB: ABSENSI & KRITIS -->
    <div id="tab-absensi" class="space-y-4">
        <!-- Perhatian Khusus -->
        <div class="card bg-red-50/50 border border-red-100">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-sm text-red-600 flex items-center gap-2"><i class="fa fa-exclamation-triangle"></i> Perhatian Khusus</h3>
                <a href="/pengasuh/track-record" class="text-[10px] text-red-500 font-bold hover:underline">Lihat Semua Profil</a>
            </div>
            <div class="space-y-2">
                @forelse($criticalSantri as $cs)
                <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-red-100 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0"><i class="fa fa-user-times text-red-500 text-xs"></i></div>
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
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-sm text-[#1B2559]">Rekap Absensi Hari Ini</h3>
                <a href="/pengasuh/absensi" class="text-[10px] text-[#4318FF] font-bold hover:underline">Detail</a>
            </div>
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

    <!-- TAB: KEUANGAN (Coming Soon) -->
    <div id="tab-keuangan" class="space-y-4 hidden">
        <div class="card text-center py-12">
            <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-300 text-3xl">
                <i class="fa fa-money-bill-wave"></i>
            </div>
            <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-1">Coming Soon</h3>
            <p class="text-xs md:text-sm text-[#A3AED0]">Modul Keuangan / Pembayaran SPP sedang dalam pengembangan.</p>
        </div>
    </div>

    <!-- TAB: PERIZINAN -->
    <div id="tab-perizinan" class="space-y-4 hidden">
        <div class="card bg-blue-50/50 border border-blue-100">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-sm text-blue-600 flex items-center gap-2"><i class="fa fa-hand-paper"></i> Menunggu & Terlambat</h3>
                <a href="/pengasuh/perizinan" class="text-[10px] text-blue-500 font-bold hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-2">
                {{-- Terlambat --}}
                @foreach($perizinanTerlambat as $p)
                <div class="p-2 bg-white rounded-xl shadow-sm border-l-2 border-red-500">
                    <div class="flex justify-between items-start mb-1 gap-2">
                        <p class="text-sm font-bold text-[#1B2559] truncate">{{ $p->santri->nama ?? $p->nis }}</p>
                        <span class="bg-red-100 text-red-600 text-[8px] font-bold px-1.5 py-0.5 rounded uppercase flex-shrink-0">Terlambat</span>
                    </div>
                    <p class="text-[10px] text-[#A3AED0] mb-1"><i class="fa fa-suitcase-rolling w-3 text-center"></i> {{ $p->jenis === 'P' ? 'Pulang' : 'Keluar' }} - {{ $p->alasanizin->nama ?? '-' }}</p>
                    <p class="text-[10px] font-bold text-red-500"><i class="fa fa-clock w-3 text-center"></i> Seharusnya kembali {{ \Carbon\Carbon::parse($p->es_kembali)->format('d/m') }}</p>
                </div>
                @endforeach

                {{-- Menunggu --}}
                @foreach($perizinanMenunggu as $p)
                <div class="p-2 bg-white rounded-xl shadow-sm border-l-2 border-yellow-500">
                    <div class="flex justify-between items-start mb-1 gap-2">
                        <p class="text-sm font-bold text-[#1B2559] truncate">{{ $p->santri->nama ?? $p->nis }}</p>
                        <span class="bg-yellow-100 text-yellow-600 text-[8px] font-bold px-1.5 py-0.5 rounded uppercase flex-shrink-0">Menunggu</span>
                    </div>
                    <p class="text-[10px] text-[#A3AED0] mb-1"><i class="fa fa-suitcase-rolling w-3 text-center"></i> {{ $p->jenis === 'P' ? 'Pulang' : 'Keluar' }} - {{ $p->alasanizin->nama ?? '-' }}</p>
                    <p class="text-[10px] text-[#2B3674]"><i class="fa fa-calendar-alt w-3 text-center"></i> {{ \Carbon\Carbon::parse($p->berangkat)->format('d/m') }} s.d {{ \Carbon\Carbon::parse($p->es_kembali)->format('d/m') }}</p>
                </div>
                @endforeach

                @if($perizinanMenunggu->isEmpty() && $perizinanTerlambat->isEmpty())
                <div class="p-3 text-center text-xs text-[#A3AED0] bg-white rounded-xl border border-gray-100">Tidak ada perizinan menunggu atau terlambat.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB: KESEHATAN (Coming Soon) -->
    <div id="tab-kesehatan" class="space-y-4 hidden">
        <div class="card text-center py-12">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-300 text-3xl">
                <i class="fa fa-heartbeat"></i>
            </div>
            <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-1">Coming Soon</h3>
            <p class="text-xs md:text-sm text-[#A3AED0]">Modul Kesehatan / IKS (Poskestren) sedang dalam pengembangan.</p>
        </div>
    </div>

    <!-- TAB: INFRASTRUKTUR (Coming Soon) -->
    <div id="tab-infrastruktur" class="space-y-4 hidden">
        <div class="card text-center py-12">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-300 text-3xl">
                <i class="fa fa-building"></i>
            </div>
            <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-1">Coming Soon</h3>
            <p class="text-xs md:text-sm text-[#A3AED0]">Modul Infrastruktur / Pembangunan sedang dalam pengembangan.</p>
        </div>
    </div>

    <!-- TAB: LOGISTIK (Coming Soon) -->
    <div id="tab-logistik" class="space-y-4 hidden">
        <div class="card text-center py-12">
            <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-300 text-3xl">
                <i class="fa fa-truck"></i>
            </div>
            <h3 class="text-sm md:text-lg font-bold text-[#1B2559] mb-1">Coming Soon</h3>
            <p class="text-xs md:text-sm text-[#A3AED0]">Modul Logistik / Distribusi sedang dalam pengembangan.</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('[id^="tab-"]').forEach(el => {
        if (el.id.startsWith('tab-')) el.classList.add('hidden');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.getElementById('btn-' + tab).classList.add('active');
}
</script>
@endpush
