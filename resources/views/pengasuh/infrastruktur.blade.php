@extends('pengasuh.layout')

@section('title', 'Infrastruktur')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Infrastruktur</h1>
@endsection

@section('content')
    @include('pengasuh._banner-tes', ['modul' => 'pembangunan & perairan'])

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <!-- Progres Pembangunan -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] flex items-center gap-2">
                    <i class="fa fa-hard-hat text-violet-500"></i> Progres Pembangunan
                </h3>
                <span class="text-[9px] bg-violet-100 text-violet-600 px-2 py-0.5 rounded-full font-bold">Kep. Pembangunan</span>
            </div>
            <div class="space-y-3">
                @php
                    $statusStyle = [
                        'Berjalan' => ['badge' => 'bg-blue-100 text-blue-600',     'bar' => 'bg-violet-500', 'text' => 'text-violet-600'],
                        'Selesai'  => ['badge' => 'bg-green-100 text-green-600',   'bar' => 'bg-green-500',  'text' => 'text-green-600'],
                        'Tertunda' => ['badge' => 'bg-yellow-100 text-yellow-600', 'bar' => 'bg-yellow-400', 'text' => 'text-yellow-600'],
                    ];
                @endphp
                @foreach($demo['pembangunan'] as $b)
                    @php $s = $statusStyle[$b['status']] ?? $statusStyle['Berjalan']; @endphp
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="flex justify-between items-center mb-2 gap-2">
                            <p class="text-sm font-bold text-[#1B2559] min-w-0 truncate">
                                <i class="fa {{ $b['icon'] }} text-violet-500 w-4 text-center mr-1"></i> {{ $b['nama'] }}
                            </p>
                            <span class="{{ $s['badge'] }} text-[8px] font-bold px-1.5 py-0.5 rounded uppercase shrink-0">{{ $b['status'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1.5">
                            <div class="{{ $s['bar'] }} h-2 rounded-full" style="width: {{ $b['progres'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span class="text-[#A3AED0]">{{ $b['ket'] }}</span>
                            <span class="font-bold {{ $s['text'] }}">{{ $b['progres'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Laporan Perairan -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] flex items-center gap-2">
                    <i class="fa fa-tint text-cyan-500"></i> Laporan Perairan
                </h3>
                <span class="text-[9px] bg-cyan-100 text-cyan-600 px-2 py-0.5 rounded-full font-bold">Kep. Perairan</span>
            </div>
            <div class="space-y-2">
                @php
                    $tingkatStyle = [
                        'kritis'  => ['border' => 'border-red-500',    'bg' => 'bg-red-100',    'text' => 'text-red-500',    'icon' => 'fa-exclamation-circle'],
                        'proses'  => ['border' => 'border-yellow-500', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'fa-wrench'],
                        'selesai' => ['border' => 'border-green-500',  'bg' => 'bg-green-100',  'text' => 'text-green-600',  'icon' => 'fa-check-circle'],
                    ];
                @endphp
                @foreach($demo['perairan'] as $l)
                    @php $s = $tingkatStyle[$l['tingkat']] ?? $tingkatStyle['proses']; @endphp
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border-l-2 {{ $s['border'] }}">
                        <div class="w-9 h-9 rounded-full {{ $s['bg'] }} flex items-center justify-center shrink-0">
                            <i class="fa {{ $s['icon'] }} {{ $s['text'] }} text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-[#1B2559]">{{ $l['judul'] }}</p>
                            <p class="text-[11px] {{ $s['text'] }} font-medium">{{ $l['ket'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
