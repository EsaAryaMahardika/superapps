@extends('pengasuh.layout')

@section('title', 'Perizinan')
@section('header')
    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">Perizinan Santri</h1>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="flex overflow-x-auto snap-x hide-scrollbar gap-3 pb-2 mb-4 -mx-4 px-4 md:mx-0 md:px-0 mt-4">
        <div class="card snap-center min-w-[100px] flex-1 text-center p-3 md:p-6">
            <p class="text-xl md:text-2xl font-bold text-[#1B2559]">{{ $countAll }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Total Izin</p>
        </div>
        <div class="card snap-center min-w-[100px] flex-1 text-center p-3 md:p-6 bg-yellow-50/50 border border-yellow-100">
            <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $countMenunggu }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Menunggu</p>
        </div>
        <div class="card snap-center min-w-[100px] flex-1 text-center p-3 md:p-6 bg-green-50/50 border border-green-100">
            <p class="text-xl md:text-2xl font-bold text-green-600">{{ $countDisetujui }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Disetujui</p>
        </div>
        <div class="card snap-center min-w-[100px] flex-1 text-center p-3 md:p-6 bg-blue-50/50 border border-blue-100">
            <p class="text-xl md:text-2xl font-bold text-blue-600">{{ $countKembali }}</p>
            <p class="text-[10px] md:text-xs text-[#A3AED0] mt-1">Kembali</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4 p-3 md:p-4">
        <form method="GET" action="/pengasuh/perizinan">
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
                    <button type="submit" class="w-full md:w-auto bg-[#4318FF] text-white text-sm rounded-xl px-4 py-2 font-medium hover:bg-[#3311CC] transition-colors whitespace-nowrap">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Filter Tabs + Search -->
    <div class="flex flex-col md:flex-row gap-3 mb-4">
        <div class="flex overflow-x-auto hide-scrollbar gap-2">
            <a href="/pengasuh/perizinan?period={{ request('period', 'bulan_ini') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ !request('status') && request('status') !== '0' ? 'bg-[#4318FF] text-white' : 'bg-white text-[#2B3674] border border-gray-200' }}">Semua</a>
            <a href="/pengasuh/perizinan?status=0&period={{ request('period', 'bulan_ini') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ request('status') === '0' ? 'bg-[#4318FF] text-white' : 'bg-white text-[#2B3674] border border-gray-200' }}">Menunggu</a>
            <a href="/pengasuh/perizinan?status=1&period={{ request('period', 'bulan_ini') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ request('status') === '1' ? 'bg-[#4318FF] text-white' : 'bg-white text-[#2B3674] border border-gray-200' }}">Disetujui</a>
            <a href="/pengasuh/perizinan?status=2&period={{ request('period', 'bulan_ini') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ request('status') === '2' ? 'bg-[#4318FF] text-white' : 'bg-white text-[#2B3674] border border-gray-200' }}">Kembali</a>
        </div>
        <form method="GET" action="/pengasuh/perizinan" class="flex-1 relative">
            <input type="hidden" name="period" value="{{ request('period', 'bulan_ini') }}">
            @if(request('status') !== null)<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari santri..."
                class="w-full bg-white border border-gray-100 text-gray-600 text-sm rounded-xl h-11 pl-10 pr-4 focus:ring-2 focus:ring-[#4318FF] focus:outline-none shadow-[0_20px_27px_0_rgba(0,0,0,0.05)]">
        </form>
    </div>

    <!-- Table & Mobile List -->
    <div class="card p-0 overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-sm md:text-lg text-[#1B2559]">Riwayat Perizinan</h3>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Nama Santri</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Jenis</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Alasan</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100">Durasi Izin</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">Status</th>
                        <th class="px-6 py-4 text-xs uppercase font-bold text-[#A3AED0] border-b border-gray-100 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perizinanList as $p)
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-[#2B3674]">{{ $p->santri->nama ?? $p->nis }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $p->jenis === 'P' ? 'bg-indigo-100 text-indigo-600' : 'bg-pink-100 text-pink-600' }}">
                                {{ $p->jenis === 'P' ? 'Pulang' : 'Keluar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#2B3674]">{{ $p->alasanizin->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-[#A3AED0]">
                            <span class="text-[#2B3674] font-medium">{{ \Carbon\Carbon::parse($p->berangkat)->format('d/m/Y') }}</span>
                            s.d {{ \Carbon\Carbon::parse($p->es_kembali)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($p->status == 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-600">Menunggu</span>
                            @elseif($p->status == 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-600">Disetujui</span>
                            @elseif($p->status == 2)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-600">Kembali</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-600">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($p->status == 0)
                                <form action="/pengasuh/perizinan/{{ $p->nis }}/action" method="POST" class="inline-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="text-xs bg-green-100 text-green-600 hover:bg-green-200 px-3 py-1.5 rounded-lg font-bold transition-all">Setujui</button>
                                </form>
                                <form action="/pengasuh/perizinan/{{ $p->nis }}/action" method="POST" class="inline-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="text-xs bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1.5 rounded-lg font-bold transition-all">Tolak</button>
                                </form>
                            @elseif($p->status == 1)
                                <form action="/pengasuh/perizinan/{{ $p->nis }}/action" method="POST" class="inline-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="action" value="kembali">
                                    <button type="submit" class="text-xs bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1.5 rounded-lg font-bold transition-all">Lapor Kembali</button>
                                </form>
                            @else
                                <span class="text-xs text-[#A3AED0]">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-[#A3AED0]">Tidak ada data perizinan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile List -->
        <div class="md:hidden flex flex-col divide-y divide-gray-100">
            @forelse($perizinanList as $p)
            <div class="p-4 bg-white">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-sm font-bold text-[#1B2559]">{{ $p->santri->nama ?? $p->nis }}</p>
                        <p class="text-[10px] text-[#A3AED0]">{{ $p->alasanizin->nama ?? '-' }}</p>
                    </div>
                    <div class="flex flex-col gap-1 items-end">
                        @if($p->status == 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-yellow-100 text-yellow-600">Menunggu</span>
                        @elseif($p->status == 1)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-green-100 text-green-600">Disetujui</span>
                        @elseif($p->status == 2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-100 text-blue-600">Kembali</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-red-100 text-red-600">Ditolak</span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $p->jenis === 'P' ? 'bg-indigo-100 text-indigo-600' : 'bg-pink-100 text-pink-600' }}">
                            {{ $p->jenis === 'P' ? 'Pulang' : 'Keluar' }}
                        </span>
                    </div>
                </div>
                <div class="bg-gray-50 p-2 rounded-lg text-center mt-2 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-[#1B2559]">{{ \Carbon\Carbon::parse($p->berangkat)->format('d/m/Y') }}</span>
                    <i class="fa fa-arrow-right text-[10px] text-[#A3AED0]"></i>
                    <span class="text-[10px] font-bold text-[#1B2559]">{{ \Carbon\Carbon::parse($p->es_kembali)->format('d/m/Y') }}</span>
                </div>
                @if($p->status == 0)
                <div class="flex gap-2 mt-3">
                    <form action="/pengasuh/perizinan/{{ $p->nis }}/action" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="w-full bg-green-500 text-white text-xs rounded-xl px-3 py-2 font-bold hover:bg-green-600 transition-colors"><i class="fa fa-check mr-1"></i>Setujui</button>
                    </form>
                    <form action="/pengasuh/perizinan/{{ $p->nis }}/action" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="w-full bg-red-500 text-white text-xs rounded-xl px-3 py-2 font-bold hover:bg-red-600 transition-colors"><i class="fa fa-times mr-1"></i>Tolak</button>
                    </form>
                </div>
                @elseif($p->status == 1)
                <div class="mt-3">
                    <form action="/pengasuh/perizinan/{{ $p->nis }}/action" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="kembali">
                        <button type="submit" class="w-full bg-[#4318FF] text-white text-xs rounded-xl px-3 py-2 font-bold hover:bg-[#3311CC] transition-colors"><i class="fa fa-undo mr-1"></i>Lapor Kembali</button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="p-8 text-center text-sm text-[#A3AED0]">Tidak ada data perizinan.</div>
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
