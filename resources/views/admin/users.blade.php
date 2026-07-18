@extends('admin.layout')
@section('title', 'Manajemen User')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Daftar Akun</h2>
    <a href="/admin/users/create" class="btn btn-primary">
        <i class="fa fa-plus"></i> Tambah Akun
    </a>
</div>

<form method="GET" action="/admin/users" class="flex gap-3 mb-6 flex-wrap items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari username..."
        class="field-input" style="width:200px;">
    <select name="role" class="field-input" style="width:160px;">
        <option value="">Semua Role</option>
        @foreach(['admin','mahadiyah','kepkam','keamanan','kantor','madin'] as $r)
            <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-dark">Filter</button>
    @if(request('q') || request('role'))
        <a href="/admin/users" class="btn btn-light">Reset</a>
    @endif
</form>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Username</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama Pengurus</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Role</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                <td class="px-4 py-3 font-mono text-xs text-[#2B3674]">{{ $u->username }}</td>
                <td class="px-4 py-3 text-[#2B3674]">{{ $u->pengurus->nama ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-[#F4F7FE] text-[#4318FF]">{{ $u->role }}</span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3 justify-end">
                        <a href="/admin/users/{{ $u->id }}/edit" class="text-xs text-[#4318FF] hover:underline">Edit</a>
                        <form method="POST" action="/admin/users/{{ $u->id }}/reset-password"
                            onsubmit="return confirm('Reset password ke 1234?')">
                            @csrf
                            <button type="submit" class="text-xs text-orange-500 hover:underline" style="background:none;border:none;cursor:pointer;">Reset PW</button>
                        </form>
                        <form method="POST" action="/admin/users/{{ $u->id }}"
                            onsubmit="return confirm('Hapus akun ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline" style="background:none;border:none;cursor:pointer;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-[#A3AED0] text-sm">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 text-sm text-[#A3AED0]">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
