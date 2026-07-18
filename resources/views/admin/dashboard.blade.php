@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Overview Akun</h2>
    <p class="text-sm text-[#A3AED0] mt-0.5">Total {{ $stats['total'] }} akun terdaftar</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    @php
        $roles = [
            'admin'     => ['label' => 'Admin',      'icon' => 'fa-shield-halved', 'color' => 'text-purple-600',  'bg' => 'bg-purple-50'],
            'mahadiyah' => ['label' => 'Mahadiyah',  'icon' => 'fa-star',          'color' => 'text-blue-600',    'bg' => 'bg-blue-50'],
            'kepkam'    => ['label' => 'Kepala Kamar','icon' => 'fa-house',         'color' => 'text-green-600',   'bg' => 'bg-green-50'],
            'keamanan'  => ['label' => 'Keamanan',   'icon' => 'fa-shield',        'color' => 'text-red-600',     'bg' => 'bg-red-50'],
            'kantor'    => ['label' => 'Kantor',      'icon' => 'fa-building',      'color' => 'text-orange-600',  'bg' => 'bg-orange-50'],
            'madin'     => ['label' => 'Madin',       'icon' => 'fa-book-open',     'color' => 'text-teal-600',    'bg' => 'bg-teal-50'],
        ];
    @endphp

    @foreach($roles as $key => $r)
    <a href="/admin/users?role={{ $key }}" class="card hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $r['bg'] }} {{ $r['color'] }} rounded-xl flex items-center justify-center shrink-0">
                <i class="fa {{ $r['icon'] }}"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-[#1B2559]">{{ $stats[$key] }}</div>
                <div class="text-xs text-[#A3AED0]">{{ $r['label'] }}</div>
            </div>
        </div>
    </a>
    @endforeach
</div>

<div class="flex justify-between items-center mb-4">
    <h3 class="text-sm font-semibold text-[#1B2559]">Akun Terbaru</h3>
    <a href="/admin/users" class="text-xs text-[#4318FF] hover:underline">Lihat semua →</a>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Username</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Nama</th>
                <th class="text-left px-4 py-3 text-xs text-[#A3AED0] font-semibold uppercase">Role</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Models\User::with('pengurus')->latest('id')->take(10)->get() as $u)
            <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                <td class="px-4 py-3 font-mono text-xs text-[#2B3674]">{{ $u->username }}</td>
                <td class="px-4 py-3 text-[#2B3674]">{{ $u->pengurus->nama ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-[#F4F7FE] text-[#4318FF]">{{ $u->role }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="/admin/users/{{ $u->id }}/edit" class="text-xs text-[#4318FF] hover:underline">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
