@extends('admin.layout')
@section('title', 'Santri — ' . $kamar->nama)

@section('content')
<div class="flex items-center gap-3 mb-5">
    <a href="/admin/asrama/{{ $kamar->asrama_id }}/kamar" class="text-[#A3AED0] hover:text-[#2B3674]">
        <i class="fa fa-arrow-left"></i>
    </a>
    <div>
        <h2 class="text-lg font-bold text-[#1B2559]">{{ $kamar->nama }}</h2>
        <p class="text-xs text-[#A3AED0]">
            {{ $kamar->asrama->nama }} —
            Kepkam: {{ $kamar->kepkam ? $kamar->kepkam->nama : 'Belum diassign' }}
            @if($kamar->kepkam?->jabatan?->divisi)
                <span class="text-[#4318FF]">({{ $kamar->kepkam->jabatan->divisi->nama }})</span>
            @endif
        </p>
    </div>
</div>

@if(!$kamar->kepkam_nis)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 mb-5 text-sm">
        <i class="fa fa-triangle-exclamation mr-2"></i>
        Kamar ini belum punya kepala kamar. Assign kepkam dulu di halaman kamar sebelum menambah santri.
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Santri dalam kamar ini --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-[#1B2559]">Santri ({{ $santri->count() }})</h3>
        </div>
        <div class="card p-0 overflow-hidden">
            @if($santri->count())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold">Nama</th>
                        <th class="text-left px-4 py-2.5 text-xs text-[#A3AED0] font-semibold">NIS</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($santri as $s)
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                        <td class="px-4 py-2.5 text-[#2B3674]">{{ $s->nama }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs text-[#A3AED0]">{{ $s->nis }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <form method="POST"
                                action="/admin/asrama/{{ $kamar->asrama_id }}/kamar/{{ $kamar->id }}/santri/{{ $s->nis }}"
                                onsubmit="return confirm('Lepas {{ $s->nama }} dari kamar ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600">Lepas</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-4 py-8 text-center text-[#A3AED0] text-sm">Belum ada santri.</div>
            @endif
        </div>
    </div>

    {{-- Assign santri baru --}}
    @if($kamar->kepkam_nis)
    <div>
        <h3 class="text-sm font-semibold text-[#1B2559] mb-3">
            Assign Santri <span class="text-[#A3AED0] font-normal">(belum punya kamar)</span>
        </h3>
        @if($semuaSantri->count())
        <form method="POST" action="/admin/asrama/{{ $kamar->asrama_id }}/kamar/{{ $kamar->id }}/santri">
            @csrf
            <div class="card p-3 mb-3 max-h-80 overflow-y-auto">
                <input type="text" id="search-santri" placeholder="Cari nama santri..."
                    class="w-full h-9 px-3 text-sm border border-gray-200 rounded-lg bg-[#F4F7FE] outline-none focus:border-[#4318FF] mb-3">
                @foreach($semuaSantri as $s)
                <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer santri-item">
                    <input type="checkbox" name="santri[]" value="{{ $s->nis }}" class="rounded">
                    <span class="text-sm text-[#2B3674] santri-nama">{{ $s->nama }}</span>
                    <span class="text-xs text-[#A3AED0] ml-auto">{{ $s->nis }}</span>
                </label>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary w-full justify-center">
                <i class="fa fa-plus text-xs"></i> Assign ke Kamar
            </button>
        </form>
        @else
        <div class="card text-center py-8 text-[#A3AED0] text-sm">
            Semua santri sudah punya kamar.
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
document.getElementById('search-santri')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.santri-item').forEach(el => {
        el.style.display = el.querySelector('.santri-nama').textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection
