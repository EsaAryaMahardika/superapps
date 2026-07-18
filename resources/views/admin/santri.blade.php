@extends('admin.layout')
@section('title', 'Data Santri')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Data Santri
        <span class="text-sm font-normal text-[#A3AED0] ml-2">{{ $santri->total() }} santri</span>
    </h2>
</div>

<form method="GET" action="/admin/santri" class="flex gap-3 mb-6 flex-wrap items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS..."
        class="field-input" style="width:220px;">
    <select name="kepkam" class="field-input" style="width:200px;">
        <option value="">Semua Kepkam</option>
        @foreach($kepkams as $k)
            <option value="{{ $k->nis }}" {{ request('kepkam') == $k->nis ? 'selected' : '' }}>
                {{ $k->nama }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-dark">Filter</button>
    @if(request('q') || request('kepkam'))
        <a href="/admin/santri" class="btn btn-light">Reset</a>
    @endif
</form>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase w-10">#</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">NIS</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Kepala Kamar</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Kelas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($santri as $s)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                <td class="px-4 py-3 text-xs text-[#A3AED0]">{{ $santri->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3 text-[#2B3674] font-medium">{{ $s->nama }}</td>
                <td class="px-4 py-3 font-mono text-xs text-[#A3AED0]">{{ $s->nis }}</td>
                <td class="px-4 py-3 text-xs text-[#A3AED0]">
                    @if($s->kepkam)
                        @php $kp = $kepkams->firstWhere('nis', $s->kepkam); @endphp
                        {{ $kp ? $kp->nama : $s->kepkam }}
                    @else
                        <span class="text-orange-400">Belum ada</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-[#A3AED0]">
                    @if($s->kepkam)
                        {{ isset($kp) ? $kp?->jabatan?->divisi?->nama ?? '-' : ($kepkams->firstWhere('nis', $s->kepkam)?->jabatan?->divisi?->nama ?? '-') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-[#A3AED0] text-sm">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($santri->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm text-[#A3AED0]">
        {{ $santri->links() }}
    </div>
    @endif
</div>
@endsection
