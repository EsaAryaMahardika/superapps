<li class="{{ Request::is('mahadiyah') ? 'active' : '' }}">
    <a href="/mahadiyah"><i class="fa fa-home"></i><span>Dashboard</span></a>
</li>

@php
    $santriActive   = Request::is('mahadiyah/absensi-mingguan*', 'mahadiyah/rekap-kegiatan*', 'mahadiyah/santri*');
    $pengurusActive = Request::is('mahadiyah/absensi-pengurus*', 'mahadiyah/rekap-absensi-pengurus*', 'mahadiyah/pengurus*');
@endphp

<li class="sidebar-group {{ $santriActive ? 'active open' : '' }}">
    <a href="javascript:void(0)" onclick="toggleSubmenu(this)" class="sidebar-group-toggle">
        <i class="fa fa-user-graduate"></i><span>Santri</span>
        <i class="fa fa-chevron-down submenu-caret ml-auto text-xs"></i>
    </a>
    <ul class="sidebar-submenu list-none p-0 m-0 {{ $santriActive ? '' : 'hidden' }}">
        <li class="{{ Request::is('mahadiyah/absensi-mingguan*') ? 'active' : '' }}">
            <a href="/mahadiyah/absensi-mingguan"><i class="fa fa-calendar-week"></i><span>Absensi Mingguan</span></a>
        </li>
        <li class="{{ Request::is('mahadiyah/rekap-kegiatan*') ? 'active' : '' }}">
            <a href="/mahadiyah/rekap-kegiatan"><i class="fa fa-chart-bar"></i><span>Rekap Absensi KepKam</span></a>
        </li>
        <li class="{{ Request::is('mahadiyah/santri*') ? 'active' : '' }}">
            <a href="/mahadiyah/santri"><i class="fa fa-user-graduate"></i><span>Data Santri</span></a>
        </li>
    </ul>
</li>

<li class="sidebar-group {{ $pengurusActive ? 'active open' : '' }}">
    <a href="javascript:void(0)" onclick="toggleSubmenu(this)" class="sidebar-group-toggle">
        <i class="fa fa-users"></i><span>Pengurus</span>
        <i class="fa fa-chevron-down submenu-caret ml-auto text-xs"></i>
    </a>
    <ul class="sidebar-submenu list-none p-0 m-0 {{ $pengurusActive ? '' : 'hidden' }}">
        <li class="{{ Request::is('mahadiyah/absensi-pengurus*') ? 'active' : '' }}">
            <a href="/mahadiyah/absensi-pengurus"><i class="fa fa-clipboard"></i><span>Absensi Pengurus</span></a>
        </li>
        <li class="{{ Request::is('mahadiyah/rekap-absensi-pengurus*') ? 'active' : '' }}">
            <a href="/mahadiyah/rekap-absensi-pengurus"><i class="fa fa-file-invoice"></i><span>Rekap Absensi Pengurus</span></a>
        </li>
        <li class="{{ Request::is('mahadiyah/pengurus*') ? 'active' : '' }}">
            <a href="/mahadiyah/pengurus"><i class="fa fa-users"></i><span>Data Pengurus</span></a>
        </li>
    </ul>
</li>