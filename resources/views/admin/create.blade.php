@extends('admin.layout')
@section('title', 'Tambah Akun')

@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="/admin/users" class="text-[#A3AED0] hover:text-[#2B3674]"><i class="fa fa-arrow-left"></i></a>
        <h2 class="text-2xl font-bold text-[#1B2559]">Tambah Akun</h2>
    </div>
    <div class="card">
        <form method="POST" action="/admin/users">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Role <span class="text-red-400">*</span></label>
                <select name="role" id="role-select" required class="field-input" onchange="toggleUsernameMode()">
                    <option value="">-- Pilih Role --</option>
                    @foreach(['admin','mahadiyah','kepkam','keamanan','kantor','madin','pengasuh'] as $r)
                        <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
                @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Username: mode pengurus (select) --}}
            <div class="mb-4" id="username-pengurus">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Pengurus <span class="text-red-400">*</span></label>
                <select name="username" id="select-pengurus" class="field-input">
                    <option value="">-- Pilih Pengurus --</option>
                    @foreach($pengurus as $p)
                        <option value="{{ $p->nis }}" {{ old('username') == $p->nis ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nis }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Username: mode pengasuh (free text) --}}
            <div class="mb-4 hidden" id="username-custom">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Username Pengasuh <span class="text-red-400">*</span></label>
                <input type="text" name="username" id="input-custom" class="field-input" placeholder="contoh: pengasuh1" value="{{ old('username') }}" disabled>
                <p class="text-xs text-[#A3AED0] mt-1">Username untuk login pengasuh (bebas, tidak harus NIS pengurus)</p>
            </div>
            @error('username') <p class="text-red-500 text-xs mt-1 -mt-2 mb-4">{{ $message }}</p> @enderror

            <div class="mb-6">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Password <span class="text-red-400">*</span></label>
                <input type="password" name="password" required minlength="4"
                    class="field-input" placeholder="Minimal 4 karakter">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/admin/users" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
function toggleUsernameMode() {
    const role = document.getElementById('role-select').value;
    const isPengasuh = role === 'pengasuh';
    const selectPengurus = document.getElementById('select-pengurus');
    const inputCustom = document.getElementById('input-custom');

    document.getElementById('username-pengurus').classList.toggle('hidden', isPengasuh);
    document.getElementById('username-custom').classList.toggle('hidden', !isPengasuh);

    // Disable yang tidak aktif agar tidak ikut submit
    selectPengurus.disabled = isPengasuh;
    inputCustom.disabled = !isPengasuh;
}

toggleUsernameMode();
</script>
@endsection
