<li class="{{ Request::is('mahadiyah') ? 'active' : '' }}">
    <a href="/mahadiyah"><i class="fa fa-home"></i><span>Dashboard</span></a>
</li>
<li class="{{ Request::is('mahadiyah/absensi-pengurus*') ? 'active' : '' }}">
    <a href="/mahadiyah/absensi-pengurus"><i class="fa fa-clipboard"></i><span>Absensi Pengurus</span></a>
</li>
<li class="{{ Request::is('mahadiyah/absensi-mingguan*') ? 'active' : '' }}">
    <a href="/mahadiyah/absensi-mingguan"><i class="fa fa-calendar-week"></i><span>Absensi Mingguan</span></a>
</li>