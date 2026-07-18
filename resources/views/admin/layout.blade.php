<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="" type="image/x-icon">
    <title>Admin — An-Nur II</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['DM Sans', 'sans-serif'] },
                    colors: {
                        primary: '#4318FF',
                        secondary: '#A3AED0',
                        dark: '#111C44',
                        light: '#F4F7FE'
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body { @apply bg-[#F4F7FE] font-sans text-[#2B3674] antialiased; }
            *, *::before, *::after { box-sizing: border-box; }
        }
        @layer components {
            .card {
                @apply bg-white rounded-[20px] shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-4 md:p-6;
            }
            .field-input {
                @apply w-full bg-[#F4F7FE] rounded-xl h-12 px-5 text-sm text-gray-600 outline-none focus:ring-2 focus:ring-[#4318FF] focus:bg-white transition-all duration-200;
                border: none;
            }
            .btn {
                @apply rounded-xl px-6 py-3 font-medium transition-all duration-200 inline-flex items-center justify-center gap-2;
                border: none;
                cursor: pointer;
            }
            .btn:hover { transform: translateY(-1px); }
            .btn-dark    { @apply bg-[#111C44] text-white hover:bg-[#1B254B] hover:shadow-lg; }
            .btn-primary { @apply bg-[#4318FF] text-white hover:bg-[#3311CC] hover:shadow-lg; }
            .btn-danger  { @apply bg-[#EE5D50] text-white hover:bg-[#D43F33] hover:shadow-lg; }
            .btn-success { @apply bg-[#05CD99] text-white hover:bg-[#04A67A] hover:shadow-lg; }
            .btn-light   { @apply bg-gray-100 text-gray-600 hover:bg-gray-200; }

            .sidebar-menu li { @apply mb-1; }
            .sidebar-menu li a {
                @apply flex items-center px-6 py-3 text-[#A3AED0] hover:text-white font-medium transition-colors duration-200 rounded-r-full mr-4 decoration-0;
                text-decoration: none !important;
            }
            .sidebar-menu li a:hover { @apply bg-white/10; }
            .sidebar-menu li a i { @apply mr-4 text-xl w-6 text-center; }
            .sidebar-menu li.active > a { @apply text-white bg-[#4318FF] shadow-md; }
            .sidebar-menu li.active > a i { @apply text-white; }
        }
    </style>
    @yield('style')
</head>

<body class="font-sans bg-[#F4F7FE] text-[#2B3674] h-screen overflow-hidden flex">

    <aside class="w-72 bg-[#111C44] flex flex-col transition-transform duration-300 z-50 shadow-xl fixed inset-y-0 left-0 -translate-x-full md:relative md:translate-x-0"
        id="sidebar-wrapper">
        <div class="h-20 flex items-center justify-between px-8 border-b border-white/5">
            <span class="text-white text-2xl font-bold tracking-wide">SuperApps<br>An-Nur II</span>
            <button class="md:hidden text-white/70 hover:text-white" onclick="toggleSidebar()">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 pl-4">
            <ul class="sidebar-menu list-none p-0 m-0">
                <li class="{{ Request::is('admin') ? 'active' : '' }}">
                    <a href="/admin"><i class="fa fa-home"></i><span>Dashboard</span></a>
                </li>
                <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                    <a href="/admin/users"><i class="fa fa-users"></i><span>Manajemen User</span></a>
                </li>
                <li class="{{ Request::is('admin/santri*') ? 'active' : '' }}">
                    <a href="/admin/santri"><i class="fa fa-user-graduate"></i><span>Data Santri</span></a>
                </li>
                <li class="{{ Request::is('admin/asrama*') ? 'active' : '' }}">
                    <a href="/admin/asrama"><i class="fa fa-building"></i><span>Asrama & Kamar</span></a>
                </li>
                <li class="{{ Request::is('admin/pengurus*') ? 'active' : '' }}">
                    <a href="/admin/pengurus"><i class="fa fa-id-card"></i><span>Data Pengurus</span></a>
                </li>
                <li class="{{ Request::is('admin/logs*') ? 'active' : '' }}">
                    <a href="/admin/logs"><i class="fa fa-clipboard-list"></i><span>Log Aktivitas</span></a>
                </li>
            </ul>

            <div class="mt-8 px-8 mb-8">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex items-center text-[#EE5D50] hover:text-[#D43F33] font-medium transition-colors"
                        style="background:none;border:none;cursor:pointer;padding:0;">
                        <i class="fa fa-power-off mr-3"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <div id="sidebar-overlay" onclick="toggleSidebar()"
        class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <header class="h-20 bg-white/50 backdrop-blur-xl flex items-center justify-between px-8 sticky top-0 z-40 border-b border-gray-100/50">
            <div class="flex items-center">
                <button class="md:hidden text-[#1B2559] text-xl mr-4" onclick="toggleSidebar()">
                    <i class="fa fa-bars"></i>
                </button>
                <div>
                    <p class="text-xs md:text-sm text-[#A3AED0] font-medium">Admin Panel</p>
                    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">
                        {{ Auth::check() ? (Auth::user()->pengurus ? Auth::user()->pengurus->nama : Auth::user()->username) : 'Admin' }}
                        <span class="text-xs md:text-sm font-normal text-[#4318FF] ml-1 md:ml-2">• Admin</span>
                    </h1>
                </div>
            </div>
            <div class="w-10 h-10 rounded-full bg-[#F4F7FE] flex items-center justify-center text-[#4318FF]">
                <i class="fa fa-user"></i>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            @if(Session::has('success'))
                <div class="bg-[#05CD99]/10 text-[#05CD99] rounded-xl p-4 mb-6 flex items-center gap-3">
                    <i class="fa fa-check-circle text-xl shrink-0"></i>
                    <div class="text-sm">{{ Session::get('success') }}</div>
                    <button class="ml-auto text-lg leading-none" onclick="this.closest('div').remove()">&times;</button>
                </div>
            @elseif(Session::has('error'))
                <div class="bg-[#EE5D50]/10 text-[#EE5D50] rounded-xl p-4 mb-6 flex items-center gap-3">
                    <i class="fa fa-exclamation-circle text-xl shrink-0"></i>
                    <div class="text-sm">{{ Session::get('error') }}</div>
                    <button class="ml-auto text-lg leading-none" onclick="this.closest('div').remove()">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        const BASE_URL = "{{ url('/') }}";
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
    @yield('script')
</body>

</html>
