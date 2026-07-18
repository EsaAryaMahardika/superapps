@extends('admin.layout')
@section('title', 'Data Pengurus')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Data Pengurus</h2>
</div>

<form method="GET" action="/admin/pengurus" class="flex gap-3 mb-6 flex-wrap items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / NIS..."
        class="field-input" style="width:220px;">
    <button type="submit" class="btn btn-dark">Cari</button>
    @if(request('q'))
        <a href="/admin/pengurus" class="btn btn-light">Reset</a>
    @endif
</form>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">NIS</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Jabatan</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Divisi</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengurus as $p)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                <td class="px-4 py-3 font-mono text-xs text-[#2B3674]">{{ $p->nis }}</td>
                <td class="px-4 py-3 text-[#2B3674]">{{ $p->nama }}</td>
                <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ $p->jabatan->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-[#A3AED0] text-xs">{{ $p->jabatan->divisi->nama ?? '-' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="/admin/pengurus/{{ $p->nis }}/edit" class="text-xs text-[#4318FF] hover:underline">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-[#A3AED0] text-sm">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($pengurus->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm text-[#A3AED0]">
        {{ $pengurus->links() }}
    </div>
    @endif
</div>
@endsection
