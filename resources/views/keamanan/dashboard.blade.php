@extends('keamanan.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Dashboard Keamanan</h2>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
    @php
        $stats = [
            ['label' => 'Santri Melanggar Bulan Ini', 'value' => $pelanggar, 'icon' => 'fa-handcuffs', 'color' => 'red'],
            ['label' => 'Santri Izin Pulang Bulan Ini', 'value' => $pulang, 'icon' => 'fa-house', 'color' => 'blue'],
            ['label' => 'Santri Izin Keluar Bulan Ini', 'value' => $keluar, 'icon' => 'fa-door-open', 'color' => 'green'],
            ['label' => 'Santri dengan SP 1', 'value' => 0, 'icon' => 'fa-triangle-exclamation', 'color' => 'yellow'],
            ['label' => 'Santri dengan SP 2', 'value' => 0, 'icon' => 'fa-triangle-exclamation', 'color' => 'orange'],
            ['label' => 'Santri dengan SP 3', 'value' => 0, 'icon' => 'fa-circle-exclamation', 'color' => 'red'],
        ];
        $colors = [
            'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-500'],
            'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-500'],
            'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-500'],
            'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-500'],
            'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-500'],
        ];
    @endphp

    @foreach($stats as $s)
    @php $c = $colors[$s['color']] ?? $colors['blue']; @endphp
    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $c['bg'] }} {{ $c['icon'] }} rounded-xl flex items-center justify-center shrink-0">
                <i class="fa {{ $s['icon'] }}"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-[#1B2559]">{{ $s['value'] }}</div>
                <div class="text-xs text-[#A3AED0]">{{ $s['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
