<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="" type="image/x-icon">
    {{-- PERBAIKAN 1: Cek apakah login untuk Title Baruuu --}}
    <title>{{ Auth::check() ? strtoupper(Auth::user()->role) : 'DASHBOARD' }} - An-Nur II</title>
    <!-- <title>{{ strtoupper($user->role) }} - An-Nur II</title> -->

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/animate-css/vivify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>

<body class="theme-light font-montserrat light_version">
    <div id="wrapper">
        <nav class="navbar top-navbar">
            <div class="container-fluid">
                <div class="navbar-left">
                    <div class="navbar-btn">
                        <button type="button" class="btn-toggle-offcanvas"><i
                                class="lnr lnr-menu fa fa-bars"></i></button>
                    </div>
                </div>
                <div class="navbar-right">
                    <div id="navbar-menu">
                        <ul class="nav navbar-nav">
                            <li><a class="icon-menu" id="mode"><i class="fa fa-2x fa-sun" id="icon"></i></a></li>
                            <li><a href="/logout" class="icon-menu"><i class="fa fa-2x fa-power-off"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div id="left-sidebar" class="sidebar">
            <div class="navbar-brand">
                <span>SuperApps An-Nur II</span>
                <button type="button" class="btn-toggle-offcanvas btn btn-sm float-right"><i
                        class="lnr lnr-menu fa fa-chevron-circle-left"></i></button>
            </div>
            <div class="sidebar-scroll">
                <div class="user-account">
                    <div class="dropdown">
                        <span>Selamat Datang</span>
                        {{-- PERBAIKAN 3: Profil User menangani Tamu --}}
                        @auth
                            <div class="user-name">
                                <strong>{{ Auth::user()->role }}</strong>
                            </div>
                            {{-- Cek apakah user punya relasi pengurus --}}
                            <span class="text-uppercase">
                                {{ Auth::user()->pengurus ? Auth::user()->pengurus->nama : Auth::user()->username }}
                            </span>
                        @else
                            <div class="user-name">
                                <strong>TAMU</strong>
                            </div>
                            <span class="text-uppercase">Pengunjung</span>
                        @endauth
                        <!-- <div class="user-name">
                            <strong>{{ $user->role }}</strong>
                        </div>
                        <span class="text-uppercase">{{ $user->pengurus->nama }}</span> -->
                    </div>
                </div>
                <nav id="left-sidebar-nav" class="sidebar-nav">
                    {{-- PERBAIKAN 4: Sidebar Menu --}}
                    @auth
                        {{-- Jika login, tampilkan menu lengkap --}}
                        @include('sidebar')
                    @else
                        {{-- Jika tamu, tampilkan menu sederhana saja --}}
                        <li class="active">
                            <a href="/mahadiyah"><i class="fa fa-home"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}"><i class="fa fa-sign-in-alt"></i> <span>Login</span></a>
                        </li>
                    @endauth
                    <!-- <ul id="main-menu" class="metismenu">
                        @include('sidebar')
                    </ul> -->
                </nav>
            </div>
        </div>
        <div id="main-content">
            @if(Session::has('success'))
                <div class="alert success-alert">
                    <p>{{ Session::get('success') }}</p>
                    <a class="close">&times;</a>
                </div>
            @elseif(Session::has('error-message'))
                <div class="alert danger-alert">
                    <p>{{ Session::get('error-message') }}</p>
                    <a class="close">&times;</a>
                </div>
            @endif
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
    <script src="{{ asset('js/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('js/vendorscripts.bundle.js') }}"></script>
    <script src="{{ asset('js/mainscripts.bundle.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('script')
</body>

</html>