@switch($user)
    @case('keamanan')
        <li><a href="/keamanan"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/perizinan"><i class="fa fa-hand"></i><span>Perizinan</span></a></li>
        <li><a href="/keamanan/pelanggaran"><i class="fa fa-handcuffs"></i><span>Pelanggaran</span></a></li>
        @break
    @case('mahadiyah')
        <li><a href="/mahadiyah"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/mahadiyah/absensi-pengurus"><i class="fa fa-list"></i><span>Absensi Pengurus</span></a></li>
        <li><a href="/mahadiyah/absensi-mingguan"><i class="fa fa-list"></i><span>Absensi Mingguan</span></a></li>
        @break
    @case('madin')
        <li><a href="/madin"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/madin/absensi-diniyah"><i class="fa fa-list"></i><span>Absensi Ngaji</span></a></li>
        <li><a href="/madin/absensi-pengajar"><i class="fa fa-list"></i><span>Absensi Pengajar</span></a></li>
        @break
    @case('kantor')
        <li><a href="/kantor"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/kantor/boyong"><i class="fa fa-home"></i><span>Boyong</span></a></li>
        @break
    @default
@endswitch