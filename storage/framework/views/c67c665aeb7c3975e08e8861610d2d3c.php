<?php switch($user):
    case ('keamanan'): ?>
        <li><a href="/keamanan"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/perizinan"><i class="fa fa-hand"></i><span>Perizinan</span></a></li>
        <li><a href="/pelanggaran"><i class="fa fa-handcuffs"></i><span>Pelanggaran</span></a></li>
        <?php break; ?>
    <?php case ('mahadiyah'): ?>
        <li><a href="/mahadiyah"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <!--<li><a href="/absensi-pengurus"><i class="fa fa-list"></i><span>Absensi Pengurus</span></a></li>-->
        <!--<li><a href="/absensi-mingguan"><i class="fa fa-list"></i><span>Absensi Mingguan</span></a></li>-->
        <li><a href="/absensi-kegiatan"><i class="fa fa-list"></i><span>Absensi Kegiatan</span></a></li>
        <?php break; ?>
    <?php case ('diniyah'): ?>
        <li><a href="/madin"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/absensi-diniyah"><i class="fa fa-list"></i><span>Absensi Ngaji</span></a></li>
        <li><a href="/absensi-pengajar"><i class="fa fa-list"></i><span>Absensi Pengajar</span></a></li>
        <?php break; ?>
    <?php case ('kantor'): ?>
        <li><a href="/kantor"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
        <li><a href="/boyong"><i class="fa fa-home"></i><span>Boyong</span></a></li>
        <?php break; ?>
    <?php default: ?>
<?php endswitch; ?><?php /**PATH C:\laragon\www\superapps\resources\views/sidebar.blade.php ENDPATH**/ ?>