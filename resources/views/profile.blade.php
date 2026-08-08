<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya - An-Nur II</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['DM Sans', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-[#F4F7FE] font-sans text-[#2B3674] min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Back --}}
    <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-sm text-[#A3AED0] hover:text-[#2B3674] mb-6 transition-colors">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>

    <div class="bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-6 md:p-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <div class="w-14 h-14 rounded-2xl bg-[#4318FF]/10 flex items-center justify-center shrink-0">
                <i class="fa fa-user text-[#4318FF] text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#1B2559]">Profil Saya</h1>
                <p class="text-sm text-[#A3AED0]">
                    {{ $user->pengurus->nama ?? $user->username }}
                    <span class="ml-1.5 px-2 py-0.5 bg-[#F4F7FE] text-[#4318FF] rounded-full text-xs font-semibold">{{ ucfirst($user->role) }}</span>
                </p>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-5 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('info'))
        <div class="mb-5 p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-700 text-sm flex items-center gap-2">
            <i class="fa fa-info-circle"></i> {{ session('info') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/profil">
            @csrf

            {{-- Info NIS --}}
            <div class="mb-5 p-4 bg-[#F4F7FE] rounded-xl">
                <p class="text-xs font-semibold text-[#A3AED0] mb-1">NIS / Username Asli</p>
                <p class="font-mono text-sm font-bold text-[#1B2559]">{{ $user->username }}</p>
                <p class="text-[11px] text-[#A3AED0] mt-1">Selalu bisa digunakan untuk login, tidak dapat diubah.</p>
            </div>

            {{-- Custom Username --}}
            <div class="mb-5">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">
                    Username Kustom
                    <span class="text-[#A3AED0] font-normal ml-1">(opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#A3AED0] text-sm">@</span>
                    <input type="text" name="custom_username"
                        value="{{ old('custom_username', $user->custom_username) }}"
                        placeholder="contoh: hanafi"
                        class="w-full bg-[#F4F7FE] border-0 rounded-xl h-12 pl-9 pr-4 text-sm text-gray-600 outline-none focus:ring-2 focus:ring-[#4318FF] focus:bg-white transition-all">
                </div>
                <p class="text-[11px] text-[#A3AED0] mt-1.5">
                    Hanya huruf, angka, titik, dan underscore. Min. 3 karakter.
                    Jika diisi, bisa digunakan untuk login selain NIS.
                </p>
                @if($user->custom_username)
                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                    <input type="checkbox" name="clear_custom_username" value="1"
                        class="w-4 h-4 text-red-500 rounded border-gray-300 focus:ring-red-400">
                    <span class="text-xs text-red-500">Hapus username kustom</span>
                </label>
                @endif
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-[#A3AED0]">Ganti Password</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Password Baru</label>
                <input type="password" name="password"
                    placeholder="Kosongkan jika tidak ingin ganti"
                    class="w-full bg-[#F4F7FE] border-0 rounded-xl h-12 px-4 text-sm text-gray-600 outline-none focus:ring-2 focus:ring-[#4318FF] focus:bg-white transition-all">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-semibold text-[#1B2559] mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    placeholder="Ulangi password baru"
                    class="w-full bg-[#F4F7FE] border-0 rounded-xl h-12 px-4 text-sm text-gray-600 outline-none focus:ring-2 focus:ring-[#4318FF] focus:bg-white transition-all">
            </div>

            <button type="submit"
                class="w-full bg-[#4318FF] hover:bg-[#3311CC] text-white py-3 rounded-xl font-semibold text-sm transition-all shadow-lg shadow-blue-500/30">
                <i class="fa fa-save mr-2"></i> Simpan Perubahan
            </button>
        </form>

        {{-- Logout: satu-satunya jalan keluar di tampilan mobile, karena
             sidebar (yang memuat tombol logout) disembunyikan di layar kecil. --}}
        <div class="mt-6 pt-6 border-t border-gray-100">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-[#EE5D50]/10 hover:bg-[#EE5D50] text-[#EE5D50] hover:text-white py-3 rounded-xl font-semibold text-sm transition-all">
                    <i class="fa fa-power-off mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
