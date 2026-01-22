<li class="{{ Request::is('/') ? 'active' : '' }}">
    <a href="/"><i class="fa fa-home"></i><span>Dashboard</span></a>
</li>
<li class="{{ Request::is('kepkam/absensi*') ? 'active' : '' }}">
    <a href="/kepkam/absensi"><i class="fa fa-clipboard"></i><span>Absensi</span></a>
</li>
<li class="{{ Request::is('kepkam/mingguan*') ? 'active' : '' }}">
    <a href="/kepkam/mingguan"><i class="fa fa-calendar-week"></i><span>Absensi Mingguan</span></a>
</li>
<li class="{{ Request::is('perizinan*') ? 'active' : '' }}">
    <a href="/perizinan"><i class="fa fa-hand-paper"></i><span>Perizinan</span></a>
</li>