<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>@yield('title', 'Pengasuh') - SuperApps An-Nur II</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['DM Sans', 'sans-serif'] }, colors: { primary: '#4318FF', secondary: '#A3AED0', dark: '#111C44', light: '#F4F7FE' } } }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base { body { @apply bg-[#F4F7FE] font-sans text-[#2B3674] antialiased pb-20 md:pb-0; } }
        @layer components {
            .card { @apply bg-white rounded-[20px] border-none shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-4 md:p-6; }
            .sidebar-menu li { @apply mb-1; }
            .sidebar-menu li a { @apply flex items-center px-6 py-3 text-[#A3AED0] hover:text-white font-medium transition-colors duration-200 rounded-r-full mr-4; text-decoration: none !important; }
            .sidebar-menu li a:hover { @apply bg-white/10; }
            .sidebar-menu li a i { @apply mr-4 text-xl w-6 text-center; }
            .sidebar-menu li.active > a { @apply text-white bg-[#4318FF] shadow-md; }
            .bottom-nav-item { @apply flex flex-col items-center justify-center w-full h-full text-[#A3AED0] transition-colors; }
            .bottom-nav-item.active { @apply text-[#4318FF] font-bold; }
            .bottom-nav-item i { @apply text-xl mb-1; }
            .bottom-nav-item span { @apply text-[10px]; }
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .tab-btn { @apply px-4 py-2 rounded-xl text-sm font-bold transition-all bg-white text-[#2B3674] border border-gray-200 whitespace-nowrap; }
            .tab-btn.active { @apply bg-[#4318FF] text-white border-[#4318FF]; }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans bg-[#F4F7FE] text-[#2B3674] h-screen overflow-hidden flex flex-col md:flex-row">

    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex w-72 bg-[#111C44] flex-col z-50 shadow-xl fixed inset-y-0 left-0">
        <div class="h-20 flex items-center justify-between px-8 border-b border-white/5">
            <span class="text-white text-2xl font-bold tracking-wide">SuperApps<br>An-Nur II</span>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 pl-4">
            <ul class="sidebar-menu list-none p-0 m-0">
                <li class="{{ request()->is('pengasuh') ? 'active' : '' }}"><a href="/pengasuh"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
                <li class="{{ request()->is('pengasuh/absensi') ? 'active' : '' }}"><a href="/pengasuh/absensi"><i class="fa fa-clipboard-list"></i><span>Rekap Absensi</span></a></li>
                <li class="{{ request()->is('pengasuh/pembayaran') ? 'active' : '' }}"><a href="/pengasuh/pembayaran"><i class="fa fa-money-bill-wave"></i><span>Pembayaran</span></a></li>
                <li class="{{ request()->is('pengasuh/perizinan') ? 'active' : '' }}"><a href="/pengasuh/perizinan"><i class="fa fa-hand-paper"></i><span>Perizinan</span></a></li>
                <li class="{{ request()->is('pengasuh/track-record') ? 'active' : '' }}"><a href="/pengasuh/track-record"><i class="fa fa-user-clock"></i><span>Track Record Santri</span></a></li>
            </ul>
            <div class="mt-8 px-8 mb-8">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-[#A3AED0] hover:text-white font-medium transition-colors">
                        <i class="fa fa-sign-out-alt mr-3"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 h-16 z-50 flex justify-around items-center px-2 pb-safe">
        <a href="/pengasuh" class="bottom-nav-item {{ request()->is('pengasuh') ? 'active' : '' }}">
            <i class="fa fa-home"></i><span>Utama</span>
        </a>
        <a href="/pengasuh/absensi" class="bottom-nav-item {{ request()->is('pengasuh/absensi') ? 'active' : '' }}">
            <i class="fa fa-clipboard-list"></i><span>Absen</span>
        </a>
        <a href="/pengasuh/pembayaran" class="bottom-nav-item {{ request()->is('pengasuh/pembayaran') ? 'active' : '' }}">
            <i class="fa fa-money-bill-wave"></i><span>Bayar</span>
        </a>
        <a href="/pengasuh/perizinan" class="bottom-nav-item {{ request()->is('pengasuh/perizinan') ? 'active' : '' }}">
            <i class="fa fa-hand-paper"></i><span>Izin</span>
        </a>
        <a href="/pengasuh/track-record" class="bottom-nav-item {{ request()->is('pengasuh/track-record') ? 'active' : '' }}">
            <i class="fa fa-user-clock"></i><span>Profil</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden md:ml-72 relative">
        <!-- Top Navbar -->
        <header class="h-16 md:h-20 bg-white/50 backdrop-blur-xl flex items-center justify-between px-4 md:px-8 sticky top-0 z-40 border-b border-gray-100/50">
            <div class="flex items-center">
                <div class="md:hidden flex items-center gap-2 mr-3">
                    <div class="w-8 h-8 bg-[#111C44] rounded-lg flex items-center justify-center text-white font-bold text-xs">AN</div>
                </div>
                <div>
                    @yield('header')
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-[#4318FF]/10 flex items-center justify-center text-[#4318FF]">
                    <i class="fa fa-user text-sm md:text-base"></i>
                </div>
            </div>
        </header>

        <!-- Content Scroll Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 hide-scrollbar">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
                    <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
                    <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif
            @yield('content')
            <div class="h-6 md:h-0"></div>
        </div>
    </main>

    @stack('scripts')
</body>
</html>
