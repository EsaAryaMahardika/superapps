<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="" type="image/x-icon">
    <title>MAHADIYAH - An-Nur II</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                    },
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
            body {
                @apply bg-[#F4F7FE] font-sans text-[#2B3674] antialiased;
            }
        }
    
        @layer components {
            .card {
                @apply bg-white rounded-[20px] border-none shadow-[0_20px_27px_0_rgba(0,0,0,0.05)] p-4 md:p-6 !important;
            }
            .form-control {
                @apply bg-[#F4F7FE] border-none text-gray-600 text-sm rounded-xl h-12 px-5 w-full focus:ring-2 focus:ring-[#4318FF] focus:bg-white transition-all duration-200 !important;
            }
            .form-control:focus {
                @apply outline-none shadow-none !important;
            }
            .btn {
                @apply rounded-xl px-6 py-3 font-medium tracking-wide transition-all duration-200 shadow-none border-none hover:-translate-y-0.5 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .btn-dark { @apply bg-[#111C44] text-white hover:bg-[#1B254B] hover:shadow-lg !important; }
            .btn-primary { @apply bg-[#4318FF] text-white hover:bg-[#3311CC] hover:shadow-lg !important; }
            .btn-danger { @apply bg-[#EE5D50] text-white hover:bg-[#D43F33] hover:shadow-lg !important; }
            .btn-success { @apply bg-[#05CD99] text-white hover:bg-[#04A67A] hover:shadow-lg !important; }
            
            /* Sidebar Links */
            .sidebar-menu li {
                @apply mb-1;
            }
            .sidebar-menu li a {
                @apply flex items-center px-6 py-3 text-[#A3AED0] hover:text-white font-medium transition-colors duration-200 rounded-r-full mr-4 relative decoration-0;
                text-decoration: none !important;
            }
            .sidebar-menu li a:hover {
                @apply bg-white/10;
            }
            .sidebar-menu li a i {
                @apply mr-4 text-xl w-6 text-center;
            }
            .sidebar-menu li.active > a {
                 @apply text-white bg-[#4318FF] border-none shadow-md;
            }
            .sidebar-menu li.active > a i {
                @apply text-white;
            }
            /* Clean up bootstrap conflicts */
            .row { @apply mx-[-12px] !important; }
            .col, [class*="col-"] { @apply px-[12px] !important; }
        }
    </style>
</head>

<body class="font-sans bg-[#F4F7FE] text-[#2B3674] h-screen overflow-hidden flex">

    <!-- Sidebar -->
    <aside
        class="w-72 bg-[#111C44] flex flex-col transition-transform duration-300 z-50 shadow-xl fixed inset-y-0 left-0 -translate-x-full md:relative md:translate-x-0"
        id="sidebar-wrapper">
        <!-- Brand -->
        <div class="h-20 flex items-center justify-between px-8 border-b border-white/5">
            <span class="text-white text-2xl font-bold tracking-wide">SuperApps<br>An-Nur II</span>
            <button class="md:hidden text-white/70 hover:text-white" onclick="toggleSidebar()">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 pl-4 custom-scrollbar">
            <ul class="sidebar-menu list-none p-0 m-0">
                @include('mahadiyah.sidebar')
            </ul>

            <!-- Bottom Actions -->
            <div class="mt-8 px-8 mb-8">
                <a href="/logout"
                    class="flex items-center text-[#EE5D50] hover:text-[#D43F33] font-medium transition-colors">
                    <i class="fa fa-power-off mr-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"
        class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Top Navbar -->
        <header
            class="h-20 bg-white/50 backdrop-blur-xl flex items-center justify-between px-8 sticky top-0 z-40 border-b border-gray-100/50">
            <div class="flex items-center">
                <button class="md:hidden text-[#1B2559] text-xl mr-4" onclick="toggleSidebar()">
                    <i class="fa fa-bars"></i>
                </button>
                <div>
                    <p class="text-xs md:text-sm text-[#A3AED0] font-medium">Selamat Datang</p>
                    <h1 class="text-sm md:text-xl font-bold text-[#1B2559]">
                        {{ Auth::check() ? (Auth::user()->pengurus ? Auth::user()->pengurus->nama : Auth::user()->username) : 'Tamu' }}
                        <span class="text-xs md:text-sm font-normal text-[#4318FF] ml-1 md:ml-2">• Mahadiyah</span>
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#F4F7FE] flex items-center justify-center text-[#4318FF]">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </header>

        <!-- Content Scroll Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
            @if(Session::has('success'))
                <div
                    class="alert alert-success bg-[#05CD99]/10 text-[#05CD99] border-none rounded-xl p-4 mb-6 flex items-center">
                    <i class="fa fa-check-circle mr-3 text-xl"></i>
                    <div>{{ Session::get('success') }}</div>
                    <button type="button" class="ml-auto" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @elseif(Session::has('error'))
                <div
                    class="alert alert-danger bg-[#EE5D50]/10 text-[#EE5D50] border-none rounded-xl p-4 mb-6 flex items-center">
                    <i class="fa fa-exclamation-circle mr-3 text-xl"></i>
                    <div>{{ Session::get('error') }}</div>
                    <button type="button" class="ml-auto" onclick="this.parentElement.remove()">&times;</button>
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
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                }, 10);
            } else {
                overlay.classList.add('opacity-0');
            }
        }
    </script>
    <script src="{{ asset('js/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('js/vendorscripts.bundle.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('script')
</body>

</html>