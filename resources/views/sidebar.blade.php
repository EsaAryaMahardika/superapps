@switch($user)
    @case('keamanan')
        <li><a href="/keamanan"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/perizinan"><i class="fa fa-hand"></i><span>Perizinan</span></a></li>
        <li><a href="/pelanggaran"><i class="fa fa-handcuffs"></i><span>Pelanggaran</span></a></li>
        @break
    @case('mahadiyah')
        <li><a href="/mahadiyah"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/absensi-pengurus"><i class="fa fa-list"></i><span>Absensi Pengurus</span></a></li>
        <li><a href="/absensi-mingguan"><i class="fa fa-list"></i><span>Absensi Mingguan</span></a></li>
        @break
    @case('diniyah')
        <li><a href="/madin"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/absensi-diniyah"><i class="fa fa-list"></i><span>Absensi Ngaji</span></a></li>
        <li><a href="/absensi-pengajar"><i class="fa fa-list"></i><span>Absensi Pengajar</span></a></li>
        @break
    @case('kantor')
        <li><a href="/dashboard"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        @break
    @default
@endswitch