@extends('pengasuh.layout')

@section('title', 'Kesehatan')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Kesehatan Santri</h1>
@endsection

@section('content')
    @include('pengasuh._banner-tes', ['modul' => 'kesehatan / Poskestren'])

    <!-- Statistik IKS -->
    <div class="grid grid-cols-3 gap-2 md:gap-4 mb-4">
        <div class="card !p-3 md:!p-5 text-center">
            <div class="w-9 h-9 md:w-12 md:h-12 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fa fa-bed text-emerald-500 text-sm md:text-lg"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-emerald-600 leading-none">{{ $demo['stats']['dirawat'] }}</p>
            <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-medium">Dirawat</p>
        </div>
        <div class="card !p-3 md:!p-5 text-center">
            <div class="w-9 h-9 md:w-12 md:h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fa fa-house-medical text-blue-500 text-sm md:text-lg"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-blue-600 leading-none">{{ $demo['stats']['rawat_jalan'] }}</p>
            <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-medium">Rawat Jalan</p>
        </div>
        <div class="card !p-3 md:!p-5 text-center">
            <div class="w-9 h-9 md:w-12 md:h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                <i class="fa fa-ambulance text-amber-500 text-sm md:text-lg"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-amber-600 leading-none">{{ $demo['stats']['dirujuk'] }}</p>
            <p class="text-[9px] md:text-xs text-[#A3AED0] mt-1 font-medium">Dirujuk RS</p>
        </div>
    </div>

    <!-- Daftar Pasien -->
    <div class="card">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-bold text-sm md:text-lg text-[#1B2559] flex items-center gap-2">
                <i class="fa fa-heartbeat text-emerald-500"></i> Santri Dalam Perawatan
            </h3>
            <span class="text-[9px] bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full font-bold">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('DD MMM YYYY') }}
            </span>
        </div>
        <div class="space-y-2">
            @php
                $tipeStyle = [
                    'dirawat'     => ['bg' => 'bg-red-100',    'text' => 'text-red-500',    'icon' => 'fa-thermometer-half'],
                    'rawat_jalan' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'fa-band-aid'],
                    'dirujuk'     => ['bg' => 'bg-blue-100',   'text' => 'text-blue-500',   'icon' => 'fa-ambulance'],
                ];
            @endphp
            @foreach($demo['pasien'] as $p)
                @php $s = $tipeStyle[$p['tipe']] ?? $tipeStyle['rawat_jalan']; @endphp
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-full {{ $s['bg'] }} flex items-center justify-center shrink-0">
                        <i class="fa {{ $s['icon'] }} {{ $s['text'] }} text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-sm font-bold text-[#1B2559] truncate">{{ $p['nama'] }}</p>
                            <span class="text-[9px] bg-white text-[#A3AED0] px-1.5 py-0.5 rounded font-bold border border-gray-200 whitespace-nowrap shrink-0">{{ $p['kelas'] }}</span>
                        </div>
                        <p class="text-[11px] {{ $s['text'] }} font-medium"><i class="fa fa-notes-medical w-3 text-center"></i> {{ $p['keterangan'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
