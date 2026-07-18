@extends('admin.layout')
@section('title', 'Log Aktivitas')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Log Aktivitas</h2>
    <p class="text-sm text-[#A3AED0] mt-0.5">Riwayat aktivitas semua user</p>
</div>

{{-- Filter --}}
<form method="GET" action="/admin/logs" class="card mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Modul</label>
            <select name="module" class="field-input h-10 text-sm">
                <option value="">Semua Modul</option>
                @foreach($modules as $mod)
                <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>
                    {{ ucfirst($mod) }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Username</label>
            <input type="text" name="username" value="{{ request('username') }}"
                placeholder="Cari username..."
                class="field-input h-10 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}"
                class="field-input h-10 text-sm">
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary py-2 px-4 text-sm">
            <i class="fa fa-search"></i> Filter
        </button>
        <a href="/admin/logs" class="btn btn-dark py-2 px-4 text-sm">
            <i class="fa fa-times"></i> Reset
        </a>
    </div>
</form>

{{-- Tabel Log --}}
<div class="card p-0 overflow-hidden">
    @if($logs->isEmpty())
        <div class="text-center py-12 text-[#A3AED0]">
            <i class="fa fa-clipboard-list text-3xl mb-2 block opacity-30"></i>
            <p class="text-sm">Belum ada aktivitas tercatat.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-[#F4F7FE]">
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase w-36">Waktu</th>
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase w-28">User</th>
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase w-24">Modul</th>
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase">Aktivitas</th>
                    <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold uppercase w-28 hidden md:table-cell">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-2.5 text-xs text-[#A3AED0] whitespace-nowrap">
                        <span title="{{ $log->created_at }}">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                        <div class="text-[10px] text-gray-300">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="font-mono text-xs text-[#2B3674] font-semibold" title="{{ $log->username }}">
                            {{ $log->username ?? '-' }}
                        </div>
                        @if(isset($namaPengurus[$log->username]))
                        <div class="text-[10px] text-[#1B2559] font-medium truncate max-w-[120px]"
                            title="{{ $namaPengurus[$log->username] }}">
                            {{ $namaPengurus[$log->username] }}
                        </div>
                        @endif
                        <div class="text-[10px] text-[#A3AED0]">{{ $log->role }}</div>
                    </td>
                    <td class="px-4 py-2.5">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $log->moduleColor }}">
                            {{ ucfirst($log->module ?? '-') }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0">
                                <i class="fa {{ $log->actionIcon }} text-[9px]"></i>
                            </span>
                            <span class="text-xs text-[#2B3674]">
                                {{ $log->description ?? $log->action }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-2.5 text-xs text-[#A3AED0] font-mono hidden md:table-cell">
                        {{ $log->ip_address ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $logs->links('pagination::simple-tailwind') }}
    </div>
    @endif
    @endif
</div>
@endsection
