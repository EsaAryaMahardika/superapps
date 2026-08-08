@extends('pengasuh.layout')

@section('title', 'Dashboard Pengasuh')
@section('header')
    <p class="text-[10px] md:text-sm text-[#A3AED0] font-medium">Selamat Datang</p>
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Pengasuh</h1>
@endsection

@section('content')
    <!-- Summary Cards — selalu muat 3 kolom, tanpa geser samping -->
    <div class="grid grid-cols-3 gap-2 md:gap-3 mb-4">
        <div class="bg-white rounded-2xl shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-3 md:px-4 md:py-3 flex flex-col md:flex-row items-center md:gap-3 text-center md:text-left">
            <div class="w-9 h-9 rounded-lg bg-[#4318FF]/10 flex items-center justify-center shrink-0 mb-1.5 md:mb-0">
                <i class="fa fa-user-graduate text-[#4318FF] text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-base md:text-lg font-bold text-[#1B2559] leading-tight">{{ $totalSantri }}</p>
                <p class="text-[9px] md:text-[10px] text-[#A3AED0] leading-tight">Total Santri</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-3 md:px-4 md:py-3 flex flex-col md:flex-row items-center md:gap-3 text-center md:text-left">
            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center shrink-0 mb-1.5 md:mb-0">
                <i class="fa fa-user-tie text-green-500 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-base md:text-lg font-bold text-[#1B2559] leading-tight">{{ $totalPengurusDalam }}</p>
                <p class="text-[9px] md:text-[10px] text-[#A3AED0] leading-tight">Pengurus Dalam</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-3 md:px-4 md:py-3 flex flex-col md:flex-row items-center md:gap-3 text-center md:text-left">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 mb-1.5 md:mb-0">
                <i class="fa fa-user-tie text-amber-500 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-base md:text-lg font-bold text-[#1B2559] leading-tight">{{ $totalPengurusLuar }}</p>
                <p class="text-[9px] md:text-[10px] text-[#A3AED0] leading-tight">Pengurus Luar</p>
            </div>
        </div>
    </div>

    <!-- Menu Grid -->
    @php
        $menuGrid = [
            ['label' => 'Absensi',        'icon' => 'fa-clipboard-list', 'bg' => 'bg-blue-50',    'fg' => 'text-blue-500',    'href' => '/pengasuh/absensi'],
            ['label' => 'Perizinan',      'icon' => 'fa-hand-paper',     'bg' => 'bg-sky-50',     'fg' => 'text-sky-500',     'href' => '/pengasuh/perizinan'],
            ['label' => 'Keuangan',       'icon' => 'fa-money-bill-wave','bg' => 'bg-orange-50',  'fg' => 'text-orange-500',  'href' => '/pengasuh/pembayaran'],
            ['label' => 'Kesehatan',      'icon' => 'fa-heartbeat',      'bg' => 'bg-emerald-50', 'fg' => 'text-emerald-500', 'href' => '/pengasuh/kesehatan'],
            ['label' => 'Infrastruktur',  'icon' => 'fa-hard-hat',       'bg' => 'bg-violet-50',  'fg' => 'text-violet-500',  'href' => '/pengasuh/infrastruktur'],
            ['label' => 'Logistik',       'icon' => 'fa-truck',          'bg' => 'bg-cyan-50',    'fg' => 'text-cyan-500',    'href' => '/pengasuh/logistik'],
            ['label' => 'Track Santri',   'icon' => 'fa-user-graduate',  'bg' => 'bg-teal-50',    'fg' => 'text-teal-500',    'href' => '/pengasuh/track-record?type=santri'],
            ['label' => 'Track Pengurus', 'icon' => 'fa-user-tie',       'bg' => 'bg-rose-50',    'fg' => 'text-rose-500',    'href' => '/pengasuh/track-record?type=pengurus'],
        ];
    @endphp
    <div class="card !p-4 md:!p-5">
        <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-y-4 gap-x-2">
            @foreach($menuGrid as $m)
                <a href="{{ $m['href'] }}" class="flex flex-col items-center gap-1.5 group no-underline">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl {{ $m['bg'] }} flex items-center justify-center transition-transform group-hover:scale-105 group-active:scale-95">
                        <i class="fa {{ $m['icon'] }} {{ $m['fg'] }} text-lg md:text-xl"></i>
                    </div>
                    <span class="text-[10px] md:text-[11px] font-medium text-[#2B3674] text-center leading-tight">{{ $m['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
