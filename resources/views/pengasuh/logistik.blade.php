@extends('pengasuh.layout')

@section('title', 'Logistik')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Logistik</h1>
@endsection

@section('content')
    @include('pengasuh._banner-tes', ['modul' => 'distribusi & laundry'])

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <!-- Distribusi Galon -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] flex items-center gap-2">
                    <i class="fa fa-bottle-water text-sky-500"></i> Distribusi Galon
                </h3>
                <span class="text-[9px] bg-sky-100 text-sky-600 px-2 py-0.5 rounded-full font-bold">Distributor</span>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="bg-sky-50 rounded-xl p-3 text-center border border-sky-100">
                    <p class="text-xl md:text-2xl font-bold text-sky-600 leading-none">{{ $demo['galon']['stats']['dikirim'] }}</p>
                    <p class="text-[9px] text-sky-600/70 font-bold uppercase tracking-wider mt-1">Dikirim</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center border border-green-100">
                    <p class="text-xl md:text-2xl font-bold text-green-600 leading-none">{{ $demo['galon']['stats']['diterima'] }}</p>
                    <p class="text-[9px] text-green-600/70 font-bold uppercase tracking-wider mt-1">Diterima</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-100">
                    <p class="text-xl md:text-2xl font-bold text-amber-600 leading-none">{{ $demo['galon']['stats']['pending'] }}</p>
                    <p class="text-[9px] text-amber-600/70 font-bold uppercase tracking-wider mt-1">Pending</p>
                </div>
            </div>
            <div class="space-y-2">
                @foreach($demo['galon']['distribusi'] as $d)
                    @php $selesai = $d['status'] === 'Selesai'; @endphp
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg {{ $selesai ? 'bg-sky-100' : 'bg-amber-100' }} flex items-center justify-center shrink-0">
                                <i class="fa fa-building {{ $selesai ? 'text-sky-500' : 'text-amber-500' }} text-[11px]"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-[#1B2559] truncate">{{ $d['asrama'] }}</p>
                                <p class="text-[10px] text-[#A3AED0]">{{ $d['jumlah'] }} galon</p>
                            </div>
                        </div>
                        <span class="{{ $selesai ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }} text-[9px] font-bold px-2 py-0.5 rounded shrink-0">
                            <i class="fa {{ $selesai ? 'fa-check' : 'fa-truck' }}"></i> {{ $d['status'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Operasional Laundry -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-sm md:text-lg text-[#1B2559] flex items-center gap-2">
                    <i class="fa fa-shirt text-pink-500"></i> Operasional Laundry
                </h3>
                <span class="text-[9px] bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full font-bold">Mgr. Laundry</span>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="bg-blue-50 rounded-xl p-3 text-center border border-blue-100">
                    <p class="text-xl md:text-2xl font-bold text-blue-600 leading-none">{{ $demo['laundry']['stats']['antrian'] }}</p>
                    <p class="text-[9px] text-blue-600/70 font-bold uppercase tracking-wider mt-1">Antrian Cuci</p>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center border border-green-100">
                    <p class="text-xl md:text-2xl font-bold text-green-600 leading-none">{{ $demo['laundry']['stats']['siap_ambil'] }}</p>
                    <p class="text-[9px] text-green-600/70 font-bold uppercase tracking-wider mt-1">Siap Ambil</p>
                </div>
            </div>
            <div class="space-y-2">
                @foreach($demo['laundry']['peralatan'] as $p)
                    @php $ok = $p['tingkat'] === 'ok'; @endphp
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-9 h-9 rounded-full {{ $ok ? 'bg-green-100' : 'bg-yellow-100' }} flex items-center justify-center shrink-0">
                            <i class="fa {{ $ok ? 'fa-circle-check text-green-500' : 'fa-exclamation-triangle text-yellow-500' }} text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1B2559]">{{ $p['nama'] }}</p>
                            <p class="text-[11px] {{ $ok ? 'text-green-600' : 'text-yellow-600' }} font-medium">{{ $p['ket'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
