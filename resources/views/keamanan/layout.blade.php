<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KEAMANAN - An-Nur II</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['DM Sans', 'sans-serif'] },
                    colors: { primary: '#4318FF', secondary: '#A3AED0', dark: '#111C44', light: '#F4F7FE' }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body { @apply bg-[#F4F7FE] font-sans text-[#2B3674] antialiased; }
        }
        @layer components {
            .card { @apply bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-4 md:p-6; }
            .field-input { @apply w-full bg-[#F4F7FE] rounded-xl h-11 px-4 text-sm text-gray-600 outline-none focus:ring-2 focus:ring-[#4318FF] focus:bg-white transition-all; border: none; }
            .btn { @apply rounded-xl px-5 py-2.5 font-semibold transition-all inline-flex items-center justify-center gap-2 text-sm; cursor: pointer; }
            .btn-primary { @apply bg-[#4318FF] text-white hover:bg-[#3311CC]; }
            .btn-dark    { @apply bg-[#111C44] text-white hover:bg-[#1B254B]; }
            .btn-danger  { @apply bg-[#EE5D50] text-white hover:bg-[#D43F33]; }
            .btn-success { @apply bg-[#05CD99] text-white hover:bg-[#04A67A]; }
            .btn-light   { @apply bg-gray-100 text-gray-600 hover:bg-gray-200; }
            .sidebar-menu li { @apply mb-1; }
            .sidebar-menu li a { @apply flex items-center px-6 py-3 text-[#A3AED0] hover:text-white font-medium transition-colors duration-200 rounded-r-full mr-4; text-decoration: none !important; }
            .sidebar-menu li a:hover { @apply bg-white/10; }
            .sidebar-menu li a i { @apply mr-4 text-xl w-6 text-center; }
            .sidebar-menu li.active > a { @apply text-white bg-[#4318FF] shadow-md; }
        }
    </style>
    @yield('style')
</head>
<body class="font-sans bg-[#F4F7FE] text-[#2B3674] h-screen overflow-hidden flex">

    <aside class="w-72 bg-[#111C44] flex flex-col transition-transform duration-300 z-50 shadow-xl fixed inset-y-0 left-0 -translate-x-full md:relative md:translate-x-0" id="sidebar-wrapper">
        <div class="h-20 flex items-center justify-between px-8 border-b border-white/5">
            <span class="text-white text-2xl font-bold tracking-wide">SuperApps<br>An-Nur II</span>
            <button class="md:hidden text-white/70 hover:text-white" onclick="toggleSidebar()">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 pl-4">
            <ul class="sidebar-menu list-none p-0 m-0">
                <li class="{{ Request::is('keamanan') ? 'active' : '' }}">
                    <a href="/keamanan"><i class="fa fa-home"></i><span>Dashboard</span></a>
                </li>
                <li class="{{ Request::is('perizinan*') ? 'active' : '' }}">
                    <a href="/perizinan"><i class="fa fa-hand"></i><span>Perizinan</span></a>
                </li>
                <li class="{{ Request::is('keamanan/pelanggaran*') ? 'active' : '' }}">
                    <a href="/keamanan/pelanggaran"><i class="fa fa-handcuffs"></i><span>Pelanggaran</span></a>
                </li>
            </ul>
            <div class="mt-8 px-8 mb-8">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex items-center text-[#EE5D50] hover:text-[#D43F33] font-medium transition-colors" style="background:none;border:none;cursor:pointer;padding:0;">
                        <i class="fa fa-power-off mr-3"></i><span>Keluar</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header class="h-20 bg-white/50 backdrop-blur-xl flex items-center justify-between px-6 md:px-8 sticky top-0 z-40 border-b border-gray-100/50">
            <div class="flex items-center">
                <button class="md:hidden text-[#1B2559] text-xl mr-4" onclick="toggleSidebar()">
                    <i class="fa fa-bars"></i>
                </button>
                <div>
                    <p class="text-xs text-[#A3AED0] font-medium">Selamat Datang</p>
                    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">{{ @session('user')->pengurus->nama ?? Auth::user()->username }}</h1>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto">
            <div class="p-4 md:p-8">
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm flex items-center gap-2">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif
                @yield('content')
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-wrapper');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            if (!overlay.classList.contains('hidden')) {
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
            }
        }
    </script>
    <script src="{{ asset('js/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('js/vendorscripts.bundle.js') }}"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('script')
</body>
</html>
