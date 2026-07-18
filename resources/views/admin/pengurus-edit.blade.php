@extends('admin.layout')
@section('title', 'Edit Pengurus')

@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-5">
        <a href="/admin/pengurus" class="text-[#A3AED0] hover:text-[#2B3674]"><i class="fa fa-arrow-left"></i></a>
        <h2 class="text-lg font-bold text-[#1B2559]">Edit Pengurus</h2>
    </div>

    <div class="card">
        <form method="POST" action="/admin/pengurus/{{ $pengurus->nis }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#2B3674] mb-1.5">NIS <span class="text-red-400">*</span></label>
                <div class="flex gap-2">
                    <input type="text" name="nis" value="{{ old('nis', $pengurus->nis) }}" required
                        class="flex-1 h-11 px-3 text-sm border border-gray-200 rounded-lg bg-[#F4F7FE] outline-none focus:border-[#4318FF] focus:bg-white"
                        id="nis-input">
                    <button type="button" onclick="generateNis(this)"
                        class="px-3 h-11 rounded-lg border border-gray-200 text-xs text-[#4318FF] hover:bg-[#F4F7FE] font-medium whitespace-nowrap">
                        <i class="fa fa-wand-magic-sparkles mr-1"></i>Generate
                    </button>
                </div>
                @error('nis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-xs font-semibold text-[#2B3674] mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $pengurus->nama) }}" required
                    class="w-full h-11 px-3 text-sm border border-gray-200 rounded-lg bg-[#F4F7FE] outline-none focus:border-[#4318FF] focus:bg-white">
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/admin/pengurus" class="btn bg-gray-100 text-gray-600 hover:bg-gray-200">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
function generateNis(btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i>...';
    fetch('/admin/generate-nis')
        .then(r => r.json())
        .then(data => { document.getElementById('nis-input').value = data.nis; })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-wand-magic-sparkles mr-1"></i>Generate';
        });
}
</script>
@endsection
