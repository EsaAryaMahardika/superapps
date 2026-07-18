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

{{-- Toolbar hapus massal (muncul saat ada yang dipilih) --}}
<div id="bulk-toolbar" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl items-center justify-between gap-3">
    <span class="text-sm text-red-700 font-semibold">
        <i class="fa fa-check-square mr-2"></i>
        <span id="selected-count">0</span> akun dipilih
    </span>
    <button onclick="confirmBulkDelete()"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2">
        <i class="fa fa-trash"></i> Hapus yang Dipilih
    </button>
</div>

<form id="form-bulk-delete" method="POST" action="/admin/users/bulk">
    @csrf
    @method('DELETE')

    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-[#F4F7FE]/50">
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" id="select-all"
                            class="w-4 h-4 text-[#4318FF] rounded border-gray-300 focus:ring-[#4318FF] cursor-pointer"
                            onchange="toggleAll(this)">
                    </th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Username</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama Pengurus</th>
                    <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Role</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors user-row cursor-pointer"
                    id="row-{{ $u->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="ids[]" value="{{ $u->id }}"
                            class="user-checkbox w-4 h-4 text-[#4318FF] rounded border-gray-300 focus:ring-[#4318FF] cursor-pointer"
                            onchange="updateBulkToolbar()">
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-[#2B3674]">{{ $u->username }}</td>
                    <td class="px-4 py-3 text-[#2B3674]">{{ $u->pengurus->nama ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-[#F4F7FE] text-[#4318FF]">{{ $u->role }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3 justify-end">
                            <a href="/admin/users/{{ $u->id }}/edit" class="text-xs text-[#4318FF] hover:underline">Edit</a>
                            <form method="POST" action="/admin/users/{{ $u->id }}/reset-password"
                                onsubmit="return confirm('Reset password akun {{ addslashes($u->username) }}?')">
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
                    <td colspan="5" class="px-4 py-8 text-center text-[#A3AED0] text-sm">Tidak ada data.</td>
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
</form>

@endsection

@section('script')
<script>
function toggleAll(master) {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = master.checked);
    updateBulkToolbar();
}

function updateBulkToolbar() {
    const checked   = document.querySelectorAll('.user-checkbox:checked');
    const toolbar   = document.getElementById('bulk-toolbar');
    const count     = document.getElementById('selected-count');
    const selectAll = document.getElementById('select-all');
    const total     = document.querySelectorAll('.user-checkbox').length;

    count.textContent = checked.length;
    toolbar.classList.toggle('hidden', checked.length === 0);
    toolbar.classList.toggle('flex', checked.length > 0);

    // Indeterminate state untuk select-all
    selectAll.indeterminate = checked.length > 0 && checked.length < total;
    selectAll.checked       = checked.length === total && total > 0;

    // Highlight baris yang dipilih
    document.querySelectorAll('.user-row').forEach(row => {
        const cb = row.querySelector('.user-checkbox');
        row.classList.toggle('bg-red-50/50', cb.checked);
    });
}

function confirmBulkDelete() {
    const count = document.querySelectorAll('.user-checkbox:checked').length;
    if (count === 0) return;
    if (confirm(`Hapus ${count} akun yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) {
        document.getElementById('form-bulk-delete').submit();
    }
}

// Klik baris untuk toggle checkbox (kecuali tombol & link)
document.querySelectorAll('.user-row').forEach(row => {
    row.addEventListener('click', function(e) {
        if (e.target.closest('button, a, form, input[type="checkbox"]')) return;
        const cb   = this.querySelector('.user-checkbox');
        cb.checked = !cb.checked;
        updateBulkToolbar();
    });
});
</script>
@endsection
