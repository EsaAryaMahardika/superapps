@extends('pengasuh.layout')

@section('title', 'Pembayaran')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Pembayaran Santri</h1>
@endsection

@section('content')
    <!-- Coming Soon State -->
    <div class="card text-center py-16 md:py-24 mt-4">
        <div class="w-20 h-20 md:w-24 md:h-24 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6 text-orange-300 text-4xl md:text-5xl">
            <i class="fa fa-money-bill-wave"></i>
        </div>
        <h3 class="text-lg md:text-2xl font-bold text-[#1B2559] mb-2">Coming Soon</h3>
        <p class="text-sm md:text-base text-[#A3AED0] max-w-md mx-auto mb-6">
            Modul Pembayaran / SPP Santri sedang dalam pengembangan. Fitur ini akan menampilkan data pembayaran, tunggakan, dan riwayat keuangan santri.
        </p>
        <div class="inline-flex flex-col gap-3 w-full max-w-sm text-left text-xs bg-orange-50 text-orange-700 p-4 rounded-xl border border-orange-100">
            <p class="font-bold text-sm"><i class="fa fa-lightbulb text-yellow-500 mr-2"></i>Fitur yang akan tersedia:</p>
            <div class="space-y-2">
                <p class="flex items-center gap-2"><i class="fa fa-check-circle text-orange-400"></i> Rekap pembayaran SPP per bulan</p>
                <p class="flex items-center gap-2"><i class="fa fa-check-circle text-orange-400"></i> Status Lunas / Cicilan / Menunggak</p>
                <p class="flex items-center gap-2"><i class="fa fa-check-circle text-orange-400"></i> Top tunggakan terbanyak</p>
                <p class="flex items-center gap-2"><i class="fa fa-check-circle text-orange-400"></i> Filter & pencarian santri</p>
                <p class="flex items-center gap-2"><i class="fa fa-check-circle text-orange-400"></i> Riwayat pembayaran per kamar</p>
            </div>
        </div>
    </div>
@endsection
