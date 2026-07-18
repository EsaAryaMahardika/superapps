@extends('admin.layout')
@section('title', 'Edit Akun')

@section('content')
<div class="max-w-lg">
    <div class="flex items-center gap-3 mb-6">
        <a href="/admin/users" class="text-[#A3AED0] hover:text-[#2B3674]"><i class="fa fa-arrow-left"></i></a>
        <h2 class="text-2xl font-bold text-[#1B2559]">Edit Akun</h2>
    </div>
    <div class="card">
        <form method="POST" action="/admin/users/{{ $user->id }}">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Pengurus <span class="text-red-400">*</span></label>
                <select name="username" required class="field-input">
                    <option value="">-- Pilih Pengurus --</option>
                    @foreach($pengurus as $p)
                        <option value="{{ $p->nis }}" {{ old('username', $user->username) == $p->nis ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ $p->nis }})
                        </option>
                    @endforeach
                </select>
                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Role <span class="text-red-400">*</span></label>
                <select name="role" required class="field-input">
                    @foreach(['admin','mahadiyah','kepkam','keamanan','kantor','madin'] as $r)
                        <option value="{{ $r }}" {{ old('role', $user->role) == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
                @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Password Baru <span class="text-[#A3AED0] font-normal">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" minlength="4"
                    class="field-input" placeholder="Kosongkan jika tidak diubah">
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
