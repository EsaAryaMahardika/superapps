<?php

namespace App\Support;

/**
 * Data dummy untuk modul pengasuh yang belum punya tabel/sumber data asli
 * (Keuangan, Kesehatan, Infrastruktur, Logistik).
 *
 * Semua yang keluar dari kelas ini ditandai "(Tes)" di tampilan. Ketika modul
 * aslinya sudah jadi, ganti pemanggilnya di PengasuhController lalu hapus
 * method yang sudah tidak dipakai di sini.
 */
class DemoPengasuh
{
    /** Tab Keuangan: tunggakan SPP terbesar + rekap status bulan berjalan. */
    public static function keuangan(): array
    {
        return [
            'tunggakan' => [
                ['nama' => 'Aji Maha Syefa', 'kelas' => '8 / SMP', 'kepkam' => 'Ust. Zaenal Fanani', 'asrama' => 'Asrama Billah 1', 'bulan' => 4, 'nominal' => 1400000],
                ['nama' => 'Achmad Hilal Abiyu Jamail', 'kelas' => '11 / SMA', 'kepkam' => 'Ust. Ihram Zulfasah E', 'asrama' => 'Asrama Billah 2', 'bulan' => 3, 'nominal' => 1050000],
                ['nama' => 'Abiyyu Zahy Akira Fawwaaz', 'kelas' => '8 / SMP', 'kepkam' => 'Ust. Zaenal Fanani', 'asrama' => 'Asrama Billah 1', 'bulan' => 2, 'nominal' => 700000],
            ],
            'status' => ['lunas' => 186, 'cicilan' => 42, 'menunggak' => 20],
        ];
    }

    /** Tab Kesehatan: rekap Poskestren (IKS) hari ini. */
    public static function kesehatan(): array
    {
        return [
            'stats' => ['dirawat' => 4, 'rawat_jalan' => 2, 'dirujuk' => 1],
            'pasien' => [
                ['nama' => 'Ahmad Fauzi', 'kelas' => '10 / SMP', 'keterangan' => 'Demam tinggi - dirawat inap sejak 06/05', 'tipe' => 'dirawat'],
                ['nama' => 'Rizki Aditya', 'kelas' => '11 / SMA', 'keterangan' => 'Keseleo kaki - rawat jalan', 'tipe' => 'rawat_jalan'],
                ['nama' => 'M. Hafiz Ramadhan', 'kelas' => '12 / SMA', 'keterangan' => 'Dirujuk ke RS Mitra Delima - tipes', 'tipe' => 'dirujuk'],
            ],
        ];
    }

    /** Tab Infrastruktur: progres pembangunan + laporan perairan. */
    public static function infrastruktur(): array
    {
        return [
            'pembangunan' => [
                ['nama' => 'Asrama Villa Baru', 'icon' => 'fa-building', 'status' => 'Berjalan', 'progres' => 68, 'ket' => 'Target: Agustus 2026'],
                ['nama' => 'Renovasi Masjid Sufin', 'icon' => 'fa-mosque', 'status' => 'Selesai', 'progres' => 100, 'ket' => 'Selesai: 02 Mei 2026'],
                ['nama' => 'Kamar Mandi Wisma', 'icon' => 'fa-toilet', 'status' => 'Tertunda', 'progres' => 30, 'ket' => 'Menunggu material'],
            ],
            'perairan' => [
                ['judul' => 'Pipa bocor - Asrama Villa Baru', 'ket' => 'Dilaporkan 06/05 - belum ditangani', 'tingkat' => 'kritis'],
                ['judul' => 'Air keruh - Kamar Mandi Ibnu Aqil', 'ket' => 'Sedang diperiksa filter', 'tingkat' => 'proses'],
                ['judul' => 'Air mati - Kamar A', 'ket' => 'Selesai ditangani', 'tingkat' => 'selesai'],
            ],
        ];
    }

    /** Tab Logistik: distribusi galon + operasional laundry. */
    public static function logistik(): array
    {
        return [
            'galon' => [
                'stats' => ['dikirim' => 45, 'diterima' => 38, 'pending' => 7],
                'distribusi' => [
                    ['asrama' => 'Asrama Billah 1', 'jumlah' => 15, 'status' => 'Selesai'],
                    ['asrama' => 'Asrama Billah Puncak', 'jumlah' => 12, 'status' => 'Selesai'],
                    ['asrama' => 'Asrama Billah 4', 'jumlah' => 10, 'status' => 'Dikirim'],
                ],
            ],
            'laundry' => [
                'stats' => ['antrian' => 32, 'siap_ambil' => 18],
                'peralatan' => [
                    ['nama' => 'Mesin cuci (4 unit)', 'ket' => 'Semua beroperasi normal', 'tingkat' => 'ok'],
                    ['nama' => 'Mesin pengering #2', 'ket' => 'Perlu perawatan - suara berisik', 'tingkat' => 'peringatan'],
                    ['nama' => 'Stok deterjen', 'ket' => 'Sisa 3 hari - segera restock', 'tingkat' => 'peringatan'],
                ],
            ],
        ];
    }

    /** Halaman Pembayaran: ringkasan, daftar transaksi, dan top tunggakan. */
    public static function pembayaran(): array
    {
        return [
            'ringkasan' => ['total' => 248, 'lunas' => 186, 'cicilan' => 42, 'menunggak' => 20],
            'daftar' => [
                ['nama' => 'Achmad Hafidz Fadli', 'kelas' => '12 / SMA', 'kamar' => 'Asrama Billah Puncak', 'kepkam' => 'Ust. M. Nuskhi J', 'jenis' => 'SPP', 'jumlah' => 350000, 'tanggal' => '04/04/2026', 'status' => 'Lunas'],
                ['nama' => 'Ahmad Binar Yudhistira', 'kelas' => '7 / SMP', 'kamar' => 'Asrama Billah 4', 'kepkam' => 'Ust. Ilyas', 'jenis' => 'SPP', 'jumlah' => 175000, 'tanggal' => '10/04/2026', 'status' => 'Cicilan'],
                ['nama' => 'Muhammad Rafa Aditya Purwanto', 'kelas' => '7 / SMP', 'kamar' => 'Asrama Billah 4', 'kepkam' => 'Ust. Hasan', 'jenis' => 'SPP', 'jumlah' => 0, 'tanggal' => '-', 'status' => 'Menunggak'],
                ['nama' => 'Achmad Hilal Abiyu Jamail', 'kelas' => '11 / SMA', 'kamar' => 'Asrama Billah 2', 'kepkam' => 'Ust. Ihram Zulfasah E', 'jenis' => 'SPP', 'jumlah' => 0, 'tanggal' => '-', 'status' => 'Menunggak'],
                ['nama' => 'Abiyyu Zahy Akira Fawwaaz', 'kelas' => '8 / SMP', 'kamar' => 'Asrama Billah 1', 'kepkam' => 'Ust. Zaenal Fanani', 'jenis' => 'SPP', 'jumlah' => 0, 'tanggal' => '-', 'status' => 'Menunggak'],
            ],
            'top_tunggakan' => [
                ['nama' => 'Irfan Maulana', 'bulan' => 4, 'persen' => 80],
                ['nama' => 'Aji Maha Syefa', 'bulan' => 4, 'persen' => 80],
                ['nama' => 'Achmad Hilal Abiyu Jamail', 'bulan' => 3, 'persen' => 60],
            ],
        ];
    }
}
