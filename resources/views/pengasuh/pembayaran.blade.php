@extends('pengasuh.layout')

@section('title', 'Pembayaran')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Pembayaran Santri</h1>
@endsection

@section('content')
    @include('pengasuh._banner-tes', ['modul' => 'keuangan'])

    <!-- Summary Cards -->
    <div class="flex overflow-x-auto snap-x hide-scrollbar gap-3 pb-2 mb-4 -mx-4 px-4 md:mx-0 md:px-0">
        <div class="card snap-center min-w-[120px] flex-1 text-center !p-3 md:!p-6">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-[#4318FF]/10 rounded-xl flex items-center justify-center mx-auto mb-2 md:mb-3"><i class="fa fa-users text-[#4318FF] text-sm md:text-xl"></i></div>
            <p class="text-xl md:text-2xl font-bold text-[#1B2559]">{{ $ringkasan['total'] }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Total Santri</p>
        </div>
        <div class="card snap-center min-w-[120px] flex-1 text-center !p-3 md:!p-6">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-2 md:mb-3"><i class="fa fa-check-circle text-green-500 text-sm md:text-xl"></i></div>
            <p class="text-xl md:text-2xl font-bold text-green-600">{{ $ringkasan['lunas'] }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Lunas</p>
        </div>
        <div class="card snap-center min-w-[120px] flex-1 text-center !p-3 md:!p-6">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-2 md:mb-3"><i class="fa fa-clock text-yellow-500 text-sm md:text-xl"></i></div>
            <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $ringkasan['cicilan'] }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Cicilan</p>
        </div>
        <div class="card snap-center min-w-[120px] flex-1 text-center !p-3 md:!p-6">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-2 md:mb-3"><i class="fa fa-exclamation-circle text-red-500 text-sm md:text-xl"></i></div>
            <p class="text-xl md:text-2xl font-bold text-red-600">{{ $ringkasan['menunggak'] }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Menunggak</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4 md:mb-6 !p-3 md:!p-4">
        <form method="GET" action="/pengasuh/pembayaran" class="flex flex-col md:flex-row gap-2 md:gap-3">
            <div class="flex-1 relative">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari santri..."
                    class="w-full bg-[#F4F7FE] border-none text-gray-600 text-sm rounded-xl h-11 pl-10 pr-4 focus:ring-2 focus:ring-[#4318FF] focus:outline-none">
            </div>
            <div class="w-full md:w-48">
                <select name="status"
                    class="w-full bg-[#F4F7FE] border-none text-gray-600 text-xs md:text-sm rounded-xl h-11 px-3 md:px-4 focus:ring-2 focus:ring-[#4318FF] focus:outline-none">
                    <option value="">Semua Status</option>
                    @foreach(['Lunas', 'Cicilan', 'Menunggak'] as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 md:flex-none bg-[#4318FF] hover:bg-[#3311CC] text-white px-5 h-11 rounded-xl font-semibold text-sm transition-all">
                    <i class="fa fa-search mr-1"></i> Cari
                </button>
                <a href="/pengasuh/pembayaran" class="flex items-center justify-center bg-gray-50 border border-gray-200 text-gray-600 hover:bg-gray-100 px-4 h-11 rounded-xl font-semibold transition-all" title="Reset filter">
                    <i class="fa fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Daftar Pembayaran -->
    <div class="card !p-0 overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-sm md:text-lg text-[#1B2559] flex items-center gap-2">
                Daftar Pembayaran
                @include('pengasuh._badge-tes')
            </h3>
            <span class="text-xs text-[#A3AED0] font-medium">{{ $daftar->count() }} data</span>
        </div>

        @php
            $statusClass = [
                'Lunas'     => 'bg-green-100 text-green-600',
                'Cicilan'   => 'bg-yellow-100 text-yellow-600',
                'Menunggak' => 'bg-red-100 text-red-600',
            ];
        @endphp

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">No</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Nama Santri</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Kamar</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Jenis</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-right">Jumlah</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftar as $i => $row)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <td class="px-6 py-4 text-sm text-[#A3AED0]">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-[#2B3674]">{{ $row['nama'] }}</span>
                                    <span class="text-[10px] bg-gray-100 text-[#A3AED0] px-2 py-0.5 rounded font-bold border border-gray-200 whitespace-nowrap">{{ $row['kelas'] }}</span>
                                </div>
                                <div class="text-xs text-[#A3AED0] flex items-center gap-1">
                                    <i class="fa fa-user-tie text-[9px]"></i> Kepala Kamar: {{ $row['kepkam'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#2B3674]">{{ $row['kamar'] }}</td>
                            <td class="px-6 py-4 text-sm text-[#2B3674]">{{ $row['jenis'] }}</td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold text-[#1B2559]">{{ $row['jumlah'] ? 'Rp ' . number_format($row['jumlah'], 0, ',', '.') : '-' }}</p>
                                <p class="text-[10px] text-[#A3AED0]">{{ $row['tanggal'] }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass[$row['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $row['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400 text-sm">Tidak ada data ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile List -->
        <div class="md:hidden flex flex-col divide-y divide-gray-100">
            @forelse($daftar as $row)
                <div class="p-4">
                    <div class="flex justify-between items-start gap-2 mb-1">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="text-sm font-bold text-[#1B2559] truncate">{{ $row['nama'] }}</p>
                                <span class="text-[9px] bg-gray-100 text-[#A3AED0] px-1.5 py-0.5 rounded font-bold border border-gray-200 whitespace-nowrap shrink-0">{{ $row['kelas'] }}</span>
                            </div>
                            <p class="text-[10px] text-[#A3AED0]"><i class="fa fa-door-closed w-3 text-center"></i> {{ $row['kamar'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase shrink-0 {{ $statusClass[$row['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $row['status'] }}</span>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <p class="text-[10px] text-[#A3AED0]">{{ $row['jenis'] }} &bull; {{ $row['tanggal'] }}</p>
                        <p class="text-sm font-bold text-[#1B2559]">{{ $row['jumlah'] ? 'Rp ' . number_format($row['jumlah'], 0, ',', '.') : '-' }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-sm">Tidak ada data ditemukan.</div>
            @endforelse
        </div>
    </div>

    <!-- Top Tunggakan -->
    <div class="mt-6">
        <h3 class="font-bold text-sm md:text-lg text-[#1B2559] mb-3 px-1 flex items-center gap-2">
            Top Tunggakan Terbanyak
            @include('pengasuh._badge-tes')
        </h3>
        <div class="flex overflow-x-auto snap-x hide-scrollbar gap-3 pb-4 -mx-4 px-4 md:mx-0 md:px-0">
            @foreach($topTunggakan as $i => $t)
                @php
                    $tone = $i === 0 ? ['bg' => '!bg-red-50 !border-red-100', 'circle' => 'bg-red-200 text-red-600', 'bar' => 'bg-red-500', 'track' => 'bg-red-100', 'text' => 'text-red-600']
                                     : ['bg' => '!bg-orange-50 !border-orange-100', 'circle' => 'bg-orange-200 text-orange-600', 'bar' => 'bg-orange-500', 'track' => 'bg-orange-100', 'text' => 'text-orange-600'];
                @endphp
                <div class="card snap-center min-w-[240px] flex-none md:flex-1 !border {{ $tone['bg'] }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $tone['circle'] }} flex items-center justify-center font-bold text-xs shrink-0">{{ $i + 1 }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-[#1B2559] text-xs truncate">{{ $t['nama'] }}</p>
                            <div class="w-full {{ $tone['track'] }} rounded-full h-1 mt-1">
                                <div class="{{ $tone['bar'] }} h-1 rounded-full" style="width: {{ $t['persen'] }}%"></div>
                            </div>
                        </div>
                        <span class="{{ $tone['text'] }} font-bold text-xs shrink-0">{{ $t['bulan'] }} bulan</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
