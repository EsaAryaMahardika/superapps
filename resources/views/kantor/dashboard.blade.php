@extends('kantor.layout')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-[#1B2559]">Dashboard Kantor</h2>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa fa-person-walking-arrow-right"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-[#1B2559]">{{ count($boyong) }}</div>
                <div class="text-xs text-[#A3AED0]">Total Santri Boyong</div>
            </div>
        </div>
    </div>
</div>
@endsection
