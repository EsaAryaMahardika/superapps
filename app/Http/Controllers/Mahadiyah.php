<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Wirid;
use App\Models\Yasinan;
use App\Models\Kegiatan;
use App\Models\Pengurus;
use App\Models\Bandongan;
use App\Models\Divisi;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class Mahadiyah extends Controller
{
    // LEGACY DASHBOARD REMOVED
    public function absensi()
    {
        // Load relasi pengurus beserta jabatan dan divisi untuk keperluan salin & edit
        $with = ['pengurus.jabatan.divisi'];
        $bandongan = Bandongan::with($with)->get();
        $wirid     = Wirid::with($with)->get();
        $yasinan   = Yasinan::with($with)->get();

        // Data semua pengurus dikelompokkan per divisi (untuk format salin)
        $divisiNon  = Divisi::where('tipe', 'non')
            ->with(['jabatan.pengurus'])
            ->orderBy('id')->get();

        $divisiKepkam = Divisi::where('tipe', 'kepkam')
            ->with(['jabatan.pengurus'])
            ->orderBy('id')->get();

        $totalSemua = Pengurus::count();
        $totalNon   = Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe','non'))->count();

        return view('mahadiyah.absensi-pengurus', compact(
            'bandongan', 'wirid', 'yasinan',
            'divisiNon', 'divisiKepkam', 'totalSemua', 'totalNon'
        ));
    }

    public function edit_absen($tipe, $tanggal)
    {
        // $tanggal dari URL format: dd-mm-yyyy → sama dengan format DB
        $tanggalDb = $tanggal; // dd-mm-yyyy

        $modelClass = match($tipe) {
            'bandongan' => Bandongan::class,
            'wirid'     => Wirid::class,
            'yasinan'   => Yasinan::class,
            default     => abort(404),
        };

        // Ambil record absensi tanggal ini, key by nis
        $existing = $modelClass::with('pengurus.jabatan.divisi')
            ->where('tanggal', $tanggalDb)
            ->get()
            ->keyBy('nis');

        // Hanya tampilkan pengurus yang sudah punya record
        $pengurus = Pengurus::with('jabatan.divisi')
            ->whereIn('nis', $existing->keys())
            ->orderBy('nama')
            ->get();

        $judulMap = ['bandongan' => 'Bandongan', 'wirid' => 'Wirid', 'yasinan' => 'Yasinan'];
        $judul    = $judulMap[$tipe];

        // Format tanggal untuk tampilan: dd-mm-yyyy → Carbon
        $tanggalDisplay = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggalDb);

        return view('mahadiyah.edit-absensi', compact(
            'tipe', 'tanggal', 'tanggalDb', 'tanggalDisplay',
            'existing', 'pengurus', 'judul'
        ));
    }

    public function update_absen(Request $request, $tipe, $tanggal)
    {
        $request->validate([
            'pengurus'   => 'required|array',
            'pengurus.*' => 'required|in:H,S,I,A',
        ]);

        // $tanggal dari URL format: dd-mm-yyyy → sama dengan format DB
        $tanggalDb = $tanggal;

        $judulMap   = ['bandongan' => 'Bandongan', 'wirid' => 'Wirid', 'yasinan' => 'Yasinan'];
        $judul      = $judulMap[$tipe] ?? $tipe;

        $modelClass = match($tipe) {
            'bandongan' => Bandongan::class,
            'wirid'     => Wirid::class,
            'yasinan'   => Yasinan::class,
            default     => abort(404),
        };

        foreach ($request->pengurus as $nis => $status) {
            $modelClass::where('nis', (string) $nis)
                ->where('tanggal', $tanggalDb)
                ->update(['status' => $status]);
        }

        session()->flash('success', "Absensi {$judul} berhasil diperbarui.");
        return redirect('/mahadiyah/absensi-pengurus');
    }
    public function create_absen()
    {
        $kegiatan = Kegiatan::where('ket', 'P')->get();

        // Semua pengurus (untuk bandongan & wirid)
        $pengurusSemua = Pengurus::with('jabatan.divisi')->orderBy('nama')->get();

        // Hanya pengurus non kepala kamar (untuk yasinan)
        $pengurusNon = Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'non'))
            ->with('jabatan.divisi')
            ->orderBy('nama')
            ->get();

        // ID kegiatan yasinan (id=9), bandongan (id=7), wirid (id=8)
        // Kirim keduanya ke view, JS yang filter sesuai pilihan kegiatan
        return view('mahadiyah.create-absensi', compact('kegiatan', 'pengurusSemua', 'pengurusNon'));
    }
    public function store_absen(Request $request)
    {
        $request->validate([
            'kegiatan'   => 'required|in:7,8,9',
            'tanggal'    => 'required|string',
            'pengurus'   => 'required|array|min:1',
            'pengurus.*' => 'required|in:H,S,I,A',
        ]);

        $kegiatan = $request->kegiatan;
        $tanggal  = $request->tanggal;

        $modelClass = match ($kegiatan) {
            '7' => Bandongan::class,
            '8' => Wirid::class,
            '9' => Yasinan::class,
        };

        if ($modelClass::where('tanggal', $tanggal)->exists()) {
            session()->flash('error', 'Absensi tanggal ini sudah dibuat');
            return redirect('/mahadiyah/absensi-pengurus');
        }

        // Yasinan: hanya pengurus non kepkam; Bandongan & Wirid: semua pengurus
        $allowedNis = ($kegiatan === '9')
            ? Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'non'))->pluck('nis')->toArray()
            : Pengurus::pluck('nis')->toArray();

        foreach ($request->pengurus as $nis => $status) {
            if (!in_array((string) $nis, $allowedNis)) continue;
            $modelClass::create(['nis' => (string) $nis, 'tanggal' => $tanggal, 'status' => $status]);
        }

        session()->flash('success', 'Absensi berhasil dibuat');
        return redirect('/mahadiyah/absensi-pengurus');
    }
    public function mingguan()
    {
        return view('mahadiyah.absen-mingguan');
    }
    public function kegiatan()
    {

    }

    // ------------------- //
    // CRUD PENGURUS        //
    // ------------------- //
    public function pengurus_index()
    {
        // Semua divisi beserta jabatan dan pengurusnya, dikelompokkan per tipe
        $divisiKepkam = Divisi::where('tipe', 'kepkam')
            ->with(['jabatan.pengurus'])
            ->orderBy('nama')
            ->get();

        $divisiNon = Divisi::where('tipe', 'non')
            ->with(['jabatan.pengurus'])
            ->orderBy('nama')
            ->get();

        // Untuk dropdown form tambah/edit pengurus
        $allJabatan = Jabatan::with('divisi')->orderBy('divisi_id')->orderBy('nama')->get();

        return view('mahadiyah.pengurus', compact('divisiKepkam', 'divisiNon', 'allJabatan'));
    }

    public function pengurus_store(Request $request)
    {
        $request->validate([
            'nis'        => 'required|string|max:20|unique:pengurus,nis',
            'nama'       => 'required|string|max:100',
            'jabatan_id' => 'nullable|exists:jabatan,id',
        ]);

        Pengurus::create([
            'nis'        => $request->nis,
            'nama'       => $request->nama,
            'jabatan_id' => $request->jabatan_id ?: null,
        ]);

        session()->flash('success', 'Pengurus berhasil ditambahkan.');
        return redirect('/mahadiyah/pengurus');
    }

    public function generateNis()
    {
        do {
            $nis = (string) random_int(100000000, 999999999);
        } while (\App\Models\Pengurus::where('nis', $nis)->exists());

        return response()->json(['nis' => $nis]);
    }

    public function pengurus_update(Request $request, $nis)
    {
        $pengurus = Pengurus::where('nis', $nis)->firstOrFail();

        $request->validate([
            'nis'        => 'required|string|max:20|unique:pengurus,nis,' . $nis . ',nis',
            'nama'       => 'required|string|max:100',
            'jabatan_id' => 'nullable|exists:jabatan,id',
        ]);

        // Kalau NIS berubah, update juga di tabel user
        if ($request->nis !== $nis) {
            \App\Models\User::where('username', $nis)->update(['username' => $request->nis]);
        }

        $pengurus->update([
            'nis'        => $request->nis,
            'nama'       => $request->nama,
            'jabatan_id' => $request->jabatan_id ?: null,
        ]);

        session()->flash('success', 'Data pengurus berhasil diperbarui.');
        return redirect('/mahadiyah/pengurus');
    }

    public function pengurus_destroy($nis)
    {
        Pengurus::where('nis', $nis)->firstOrFail()->delete();
        session()->flash('success', 'Pengurus berhasil dihapus.');
        return redirect('/mahadiyah/pengurus');
    }

    // ------------------- //
    // CRUD DIVISI          //
    // ------------------- //
    public function divisi_store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:kepkam,non',
        ]);

        Divisi::create(['nama' => $request->nama, 'tipe' => $request->tipe]);

        session()->flash('success', 'Divisi berhasil ditambahkan.');
        return redirect('/mahadiyah/pengurus');
    }

    public function divisi_update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:kepkam,non',
        ]);

        Divisi::findOrFail($id)->update(['nama' => $request->nama, 'tipe' => $request->tipe]);

        session()->flash('success', 'Divisi berhasil diperbarui.');
        return redirect('/mahadiyah/pengurus');
    }

    public function divisi_destroy($id)
    {
        Divisi::findOrFail($id)->delete();
        session()->flash('success', 'Divisi berhasil dihapus.');
        return redirect('/mahadiyah/pengurus');
    }

    // ------------------- //
    // CRUD JABATAN         //
    // ------------------- //
    public function jabatan_store(Request $request)
    {
        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'nama'      => 'required|array|min:1',
            'nama.*'    => 'required|string|max:100',
        ]);

        $divisiId = $request->divisi_id;
        $now = now();

        // Filter baris kosong, lalu bulk insert
        $rows = collect($request->nama)
            ->filter(fn($n) => trim($n) !== '')
            ->map(fn($n) => [
                'divisi_id'  => $divisiId,
                'nama'       => trim($n),
                'created_at' => $now,
                'updated_at' => $now,
            ])->values()->all();

        if (empty($rows)) {
            session()->flash('error', 'Minimal isi satu nama jabatan.');
            return redirect('/mahadiyah/pengurus');
        }

        Jabatan::insert($rows);

        $count = count($rows);
        session()->flash('success', "$count jabatan berhasil ditambahkan.");
        return redirect('/mahadiyah/pengurus');
    }

    public function jabatan_update(Request $request, $id)
    {
        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'nama'      => 'required|string|max:100',
        ]);

        Jabatan::findOrFail($id)->update(['divisi_id' => $request->divisi_id, 'nama' => $request->nama]);

        session()->flash('success', 'Jabatan berhasil diperbarui.');
        return redirect('/mahadiyah/pengurus');
    }

    public function jabatan_destroy($id)
    {
        Jabatan::findOrFail($id)->delete();
        session()->flash('success', 'Jabatan berhasil dihapus.');
        return redirect('/mahadiyah/pengurus');
    }

    // ------------------- //
    // REKAP KEGIATAN KEPALA KAMAR //
    // ------------------- //
    public function rekapKegiatan(Request $request)
    {
        // Get date range or default to last 7 days
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $startDate = $request->input('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(6);

        // Generate array of dates in range
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d/m/Y');
            $currentDate->addDay();
        }

        $totalDays = count($dates);

        // Get all kepala kamar
        $kepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nama', 'p.nis')
            ->orderBy('p.nama')
            ->get();

        // Define all activities to check
        $activities = [
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        // Build recap data for each kepala kamar
        $rekapData = [];
        foreach ($kepkams as $kepkam) {
            $row = [
                'nis' => $kepkam->nis,
                'nama' => $kepkam->nama,
                'daily_status' => [],
                'total' => 0,
                'percentage' => 0
            ];

            // Check each date
            foreach ($dates as $date) {
                $hasFilled = $this->checkKepkamActivity($kepkam->nis, $date, $activities);
                $row['daily_status'][$date] = $hasFilled;
                if ($hasFilled) {
                    $row['total']++;
                }
            }

            // Calculate current percentage
            $row['percentage'] = $totalDays > 0 ? round(($row['total'] / $totalDays) * 100) : 0;

            $rekapData[] = $row;
        }

        return view('mahadiyah.rekap-kegiatan', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));
    }

    private function checkKepkamActivity($kepkamNis, $tanggal, $activities)
    {
        // Get santri under this kepkam
        $santriNis = DB::table('santri')
            ->where('kepkam', $kepkamNis)
            ->pluck('nis')
            ->toArray();

        if (empty($santriNis)) {
            return false;
        }

        // Check if any activity has attendance for this date
        foreach ($activities as $activity) {
            $modelClass = "\\App\\Models\\{$activity['model']}";
            $query = $modelClass::where('tanggal', $tanggal)
                ->whereIn('nis', $santriNis);

            if ($activity['col']) {
                $query->where($activity['col'], $activity['val']);
            }

            if ($query->exists()) {
                return true;
            }
        }

        return false;
    }

    public function downloadRekapKegiatan(Request $request)
    {
        try {
            // Get date range or default to last 7 days
            $endDate = $request->input('end_date')
                ? Carbon::parse($request->end_date)
                : Carbon::now();

            $startDate = $request->input('start_date')
                ? Carbon::parse($request->start_date)
                : Carbon::now()->subDays(6);

            // Generate array of dates in range
            $dates = [];
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dates[] = $currentDate->format('d/m/Y');
                $currentDate->addDay();
            }

            $totalDays = count($dates);

            // Get all kepala kamar
            $kepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
                ->where('user.role', 'kepkam')
                ->select('p.nama', 'p.nis')
                ->orderBy('p.nama')
                ->get();

            // Define all activities to check
            $activities = [
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
                ['model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
                ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
                ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
            ];

            // Build recap data for each kepala kamar
            $rekapData = [];
            foreach ($kepkams as $kepkam) {
                $row = [
                    'nis' => $kepkam->nis,
                    'nama' => $kepkam->nama,
                    'daily_status' => [],
                    'total' => 0,
                    'percentage' => 0
                ];

                // Check each date
                foreach ($dates as $date) {
                    $hasFilled = $this->checkKepkamActivity($kepkam->nis, $date, $activities);
                    $row['daily_status'][$date] = $hasFilled;
                    if ($hasFilled) {
                        $row['total']++;
                    }
                }

                // Calculate current percentage
                $row['percentage'] = $totalDays > 0 ? round(($row['total'] / $totalDays) * 100) : 0;

                $rekapData[] = $row;
            }

            // Load PDF view
            $pdf = Pdf::loadView('mahadiyah.rekap-kegiatan-pdf', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));

            // Set landscape orientation for better table display
            $pdf->setPaper('a4', 'landscape');

            // Download PDF
            $filename = 'Rekap_Kegiatan_KepKam_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('[PDF Download] Error generating PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // ------------------- //
    // MAIN DASHBOARD - RINGKASAN ABSENSI //
    // ------------------- //
    public function dashboard(Request $request)
    {
        // Resilient Date Parsing
        $summaryDate = $request->filled('summary_date')
            ? Carbon::parse($request->summary_date)
            : Carbon::now();
        $today = $summaryDate->format('d/m/Y');

        // Chart Date Range Logic
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        }

        // Get all kepala kamar with names
        $allKepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nis', 'p.nama')
            ->orderBy('p.nama')
            ->get();

        $totalKepkam = $allKepkams->count();

        // Check which kepkam has filled attendance today (any activity)
        $activities = [
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2, 'label' => 'Subuh'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3, 'label' => 'Dhuhur'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4, 'label' => 'Ashar'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5, 'label' => 'Maghrib'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6, 'label' => 'Isya'],
            ['model' => 'AbsensiWaqiah', 'col' => null, 'val' => null, 'label' => 'Waqiah'],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10, 'label' => 'Ngaji Sore'],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11, 'label' => 'Ngaji Malam'],
        ];

        $kepkamSudahAbsenCount = 0;
        $kepkamBelumAbsenCount = 0;
        $listKepkamSudah = [];
        $listKepkamBelum = [];

        foreach ($allKepkams as $kepkam) {
            $activitiesDone = [];
            $activitiesMissing = [];

            $santriNis = DB::table('santri')
                ->where('kepkam', $kepkam->nis)
                ->pluck('nis')
                ->toArray();

            foreach ($activities as $act) {
                $isFilled = false;
                if (!empty($santriNis)) {
                    $modelClass = "\\App\\Models\\{$act['model']}";
                    $query = $modelClass::where('tanggal', $today)->whereIn('nis', $santriNis);
                    if ($act['col']) {
                        $query->where($act['col'], $act['val']);
                    }
                    $isFilled = $query->exists();
                }

                if ($isFilled) {
                    $activitiesDone[] = "✅ " . $act['label'];
                } else {
                    $activitiesMissing[] = "❌ " . $act['label'];
                }
            }

            if (!empty($activitiesDone)) {
                $kepkamSudahAbsenCount++;
                $listKepkamSudah[] = [
                    'nama' => $kepkam->nama,
                    'kegiatan' => $activitiesDone
                ];
            }

            if (!empty($activitiesMissing)) {
                $kepkamBelumAbsenCount++;
                $listKepkamBelum[] = [
                    'nama' => $kepkam->nama,
                    'kegiatan' => $activitiesMissing
                ];
            }
        }

        $kepkamSudahAbsen = $kepkamSudahAbsenCount;
        $kepkamBelumAbsen = $kepkamBelumAbsenCount;

        // Collect unique santri hadir, sakit, izin, alpa today with activity details
        $listSantriHadirNis = collect();
        $listSantriSakitDetail = []; // nis => [activities]
        $listSantriIzinDetail = [];
        $listSantriAlpaDetail = [];

        $activityMap = [
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 2, 'label' => 'Subuh'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 3, 'label' => 'Dhuhur'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 4, 'label' => 'Ashar'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 5, 'label' => 'Maghrib'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 6, 'label' => 'Isya'],
            ['table' => 'absen_waqiah', 'col' => null, 'val' => null, 'label' => 'Waqiah'],
            ['table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 10, 'label' => 'Ngaji Sore'],
            ['table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 11, 'label' => 'Ngaji Malam'],
        ];

        foreach ($activityMap as $act) {
            $query = DB::table($act['table'])->where('tanggal', $today);
            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }

            // Hadir unique
            $hadirNis = (clone $query)->where('status', 'H')->pluck('nis');
            $listSantriHadirNis = $listSantriHadirNis->merge($hadirNis);

            // Sakit with activity detail
            $sakitNis = (clone $query)->where('status', 'S')->pluck('nis');
            foreach ($sakitNis as $nis) {
                $listSantriSakitDetail[$nis][] = $act['label'];
            }

            // Izin with activity detail
            $izinNis = (clone $query)->where('status', 'I')->pluck('nis');
            foreach ($izinNis as $nis) {
                $listSantriIzinDetail[$nis][] = $act['label'];
            }

            // Alpa with activity detail
            $alpaNis = (clone $query)->where('status', 'A')->pluck('nis');
            foreach ($alpaNis as $nis) {
                $listSantriAlpaDetail[$nis][] = $act['label'];
            }
        }

        // Unique counts
        $santriHadir = $listSantriHadirNis->unique()->count();
        $santriSakit = count($listSantriSakitDetail);
        $santriIzin = count($listSantriIzinDetail);
        $santriAlpa = count($listSantriAlpaDetail);

        // Helper to Group by Kepkam
        $groupByKepkam = function ($query, $isSimpleList = false, $detailsMap = []) {
            $data = $query
                ->leftJoin('pengurus', 'santri.kepkam', '=', 'pengurus.nis')
                ->select('santri.nis', 'santri.nama', 'pengurus.nama as kepkam_nama')
                ->orderBy('kepkam_nama')
                ->orderBy('santri.nama')
                ->get();

            $grouped = [];
            foreach ($data as $row) {
                $kepkamName = $row->kepkam_nama ?? 'Tanpa Kepkam';

                if ($isSimpleList) {
                    $grouped[$kepkamName][] = $row->nama;
                } else {
                    $activities = $detailsMap[$row->nis] ?? [];
                    $grouped[$kepkamName][] = [
                        'nama' => $row->nama,
                        'kegiatan' => $activities
                    ];
                }
            }
            ksort($grouped);

            $final = [];
            foreach ($grouped as $k => $list) {
                $final[] = ['kepkam' => $k, 'list' => $list];
            }
            return $final;
        };

        // Get Hadir Groups
        $hadirNisList = $listSantriHadirNis->unique();
        $listSantriHadirNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $hadirNisList),
            true,
            []
        );

        // Get Sakit Groups
        $sakitNisList = array_keys($listSantriSakitDetail);
        $listSantriSakitNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $sakitNisList),
            false,
            $listSantriSakitDetail
        );

        // Get Izin Groups
        $izinNisList = array_keys($listSantriIzinDetail);
        $listSantriIzinNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $izinNisList),
            false,
            $listSantriIzinDetail
        );

        // Get Alpa Groups
        $alpaNisList = array_keys($listSantriAlpaDetail);
        $listSantriAlpaNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $alpaNisList),
            false,
            $listSantriAlpaDetail
        );

        // Chart data: last 7 days, hadir per activity
        // Chart data: based on selected range
        $chartLabels = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $chartLabels[] = $curr->format('d/m/Y');
            $curr->addDay();
        }

        $chartActivities = [
            ['key' => 'Subuh', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 2],
            ['key' => 'Dhuhur', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 3],
            ['key' => 'Ashar', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 4],
            ['key' => 'Maghrib', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 5],
            ['key' => 'Isya', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 6],
            ['key' => 'Waqiah', 'table' => 'absen_waqiah', 'col' => null, 'val' => null],
            ['key' => 'Ngaji Sore', 'table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 10],
            ['key' => 'Ngaji Malam', 'table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $chartDatasets = [];
        foreach ($chartActivities as $act) {
            $data = [];
            foreach ($chartLabels as $date) {
                $query = DB::table($act['table'])
                    ->where('tanggal', $date)
                    ->where('status', 'H');
                if ($act['col']) {
                    $query->where($act['col'], $act['val']);
                }
                $data[] = $query->count();
            }
            $chartDatasets[] = [
                'label' => $act['key'],
                'data' => $data,
            ];
        }

        // Format chart labels for display (dd MMM)
        $chartLabelsFormatted = array_map(function ($d) {
            return Carbon::createFromFormat('d/m/Y', $d)->format('d M');
        }, $chartLabels);

        // Total Santri Count
        $totalSantri = DB::table('santri')->count();

        // Date Range for Ranking (Resilient Parsing)
        $rankStartDate = $request->filled('rank_start_date')
            ? Carbon::parse($request->rank_start_date)->startOfDay()
            : $startDate->copy();
        $rankEndDate = $request->filled('rank_end_date')
            ? Carbon::parse($request->rank_end_date)->endOfDay()
            : $endDate->copy();

        $dateRangeRanking = [];
        $currRank = $rankStartDate->copy();
        while ($currRank->lte($rankEndDate)) {
            $dateRangeRanking[] = $currRank->format('d/m/Y');
            $currRank->addDay();
        }

        $topProblematic = DB::table(function ($query) use ($dateRangeRanking) {
            $query->select('nis')
                ->from('absen_jamaah')
                ->where('status', 'A')
                ->whereIn('tanggal', $dateRangeRanking)
                ->unionAll(
                    DB::table('absen_waqiah')
                        ->select('nis')
                        ->where('status', 'A')
                        ->whereIn('tanggal', $dateRangeRanking)
                )
                ->unionAll(
                    DB::table('absen_ngaji')
                        ->select('nis')
                        ->where('status', 'A')
                        ->whereIn('tanggal', $dateRangeRanking)
                );
        }, 'all_alpa')
            ->select('nis', DB::raw('count(*) as alpa_count'))
            ->groupBy('nis')
            ->orderByDesc('alpa_count')
            ->limit(5)
            ->get();

        $topRanking = [];
        foreach ($topProblematic as $tp) {
            $santri = DB::table('santri')
                ->leftJoin('pengurus', 'santri.kepkam', '=', 'pengurus.nis')
                ->where('santri.nis', $tp->nis)
                ->select('santri.nama', 'pengurus.nama as kepkam_nama')
                ->first();

            if ($santri) {
                $santriDetails = [];

                // Fetch Details from absen_jamaah
                $details_jamaah = DB::table('absen_jamaah')
                    ->where('nis', $tp->nis)
                    ->where('status', 'A')
                    ->whereIn('tanggal', $dateRangeRanking)
                    ->select('tanggal', 'sholat as val')
                    ->get();
                foreach ($details_jamaah as $d) {
                    $names = [1 => 'Subuh', 2 => 'Subuh', 3 => 'Dhuhur', 4 => 'Ashar', 5 => 'Maghrib', 6 => 'Isya'];
                    $sholatName = $names[$d->val] ?? 'Sholat';
                    $santriDetails[] = [
                        'tanggal' => $d->tanggal,
                        'kegiatan' => $sholatName,
                        'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_" . $d->val
                    ];
                }

                // Fetch Details from absen_waqiah
                $details_waqiah = DB::table('absen_waqiah')
                    ->where('nis', $tp->nis)
                    ->where('status', 'A')
                    ->whereIn('tanggal', $dateRangeRanking)
                    ->select('tanggal')
                    ->get();
                foreach ($details_waqiah as $d) {
                    $santriDetails[] = [
                        'tanggal' => $d->tanggal,
                        'kegiatan' => "Waqiah",
                        'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_7"
                    ];
                }

                // Fetch Details from absen_ngaji
                $details_ngaji = DB::table('absen_ngaji')
                    ->where('nis', $tp->nis)
                    ->where('status', 'A')
                    ->whereIn('tanggal', $dateRangeRanking)
                    ->select('tanggal', 'ngaji as val')
                    ->get();
                foreach ($details_ngaji as $d) {
                    $names = [10 => 'Ngaji Sore', 11 => 'Ngaji Malam'];
                    $ngajiName = $names[$d->val] ?? 'Ngaji';
                    $santriDetails[] = [
                        'tanggal' => $d->tanggal,
                        'kegiatan' => $ngajiName,
                        'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_" . $d->val
                    ];
                }

                // Sort details by date descending
                usort($santriDetails, function ($a, $b) {
                    return strcmp($b['raw_date'], $a['raw_date']);
                });

                // Group by Full Date (Day, Date) in Indonesian
                $grouped = [];
                Carbon::setLocale('id');
                foreach ($santriDetails as $d) {
                    $fullDate = Carbon::createFromFormat('d/m/Y', $d['tanggal'])->translatedFormat('l, d F Y');
                    if (!isset($grouped[$fullDate])) {
                        $grouped[$fullDate] = [
                            'nama' => $fullDate,
                            'kegiatan' => []
                        ];
                    }
                    $grouped[$fullDate]['kegiatan'][] = $d['kegiatan'];
                }

                $topRanking[] = [
                    'nama' => $santri->nama,
                    'kepkam' => $santri->kepkam_nama ?? 'Tanpa Kepkam',
                    'count' => $tp->alpa_count,
                    'details' => array_values($grouped)
                ];
            }
        }

        return view('mahadiyah.dashboard', compact(
            'kepkamSudahAbsen',
            'kepkamBelumAbsen',
            'santriHadir',
            'santriSakit',
            'santriIzin',
            'santriAlpa',
            'listKepkamSudah',
            'listKepkamBelum',
            'listSantriHadirNama',
            'listSantriSakitNama',
            'listSantriIzinNama',
            'listSantriAlpaNama',
            'chartLabelsFormatted',
            'chartDatasets',
            'totalKepkam',
            'totalSantri',
            'startDate',
            'endDate',
            'summaryDate',
            'topRanking',
            'rankStartDate',
            'rankEndDate'
        ));
    }

    public function rekapAbsensiPengurus(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfWeek();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now()->endOfWeek();

        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-m-Y');
            $currentDate->addDay();
        }

        $totalDays = count($dates);

        $pengurusList = Pengurus::with(['jabatan.divisi'])
            ->get()
            ->sortBy(function($p) {
                $tipe = $p->jabatan->divisi->tipe ?? 'z_none';
                $divName = $p->jabatan->divisi->nama ?? 'z_none';
                $jabName = $p->jabatan->nama ?? 'z_none';
                $tipeOrder = $tipe === 'kepkam' ? 0 : 1;
                return sprintf('%d_%s_%s_%s', $tipeOrder, $divName, $jabName, $p->nama);
            });

        $nisList = $pengurusList->pluck('nis')->toArray();

        $bandonganData = DB::table('bandongan')
            ->whereIn('tanggal', $dates)
            ->whereIn('nis', $nisList)
            ->get()
            ->groupBy('nis');

        $wiridData = DB::table('wirid')
            ->whereIn('tanggal', $dates)
            ->whereIn('nis', $nisList)
            ->get()
            ->groupBy('nis');

        $yasinanData = DB::table('yasinan')
            ->whereIn('tanggal', $dates)
            ->whereIn('nis', $nisList)
            ->get()
            ->groupBy('nis');

        $rekapData = [];
        foreach ($pengurusList as $p) {
            $nis = $p->nis;
            $div = $p->jabatan->divisi ?? null;
            $tipe = $div ? $div->tipe : 'non';

            $row = [
                'nis' => $p->nis,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan->nama ?? 'Pengurus',
                'divisi' => $div->nama ?? 'Tanpa Divisi',
                'tipe' => $tipe,
                'attendance' => [],
                'summary' => [
                    'bandongan' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                    'wirid' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                    'yasinan' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                ]
            ];

            $pBandongan = isset($bandonganData[$nis]) ? $bandonganData[$nis]->keyBy('tanggal') : collect();
            $pWirid = isset($wiridData[$nis]) ? $wiridData[$nis]->keyBy('tanggal') : collect();
            $pYasinan = isset($yasinanData[$nis]) ? $yasinanData[$nis]->keyBy('tanggal') : collect();

            foreach ($dates as $date) {
                $bStatus = $pBandongan->get($date)->status ?? null;
                $wStatus = $pWirid->get($date)->status ?? null;
                $yStatus = ($tipe === 'kepkam') ? null : ($pYasinan->get($date)->status ?? null);

                $row['attendance'][$date] = [
                    'bandongan' => $bStatus,
                    'wirid' => $wStatus,
                    'yasinan' => $yStatus,
                ];

                if ($bStatus) {
                    $row['summary']['bandongan'][$bStatus]++;
                    $row['summary']['bandongan']['total']++;
                }
                if ($wStatus) {
                    $row['summary']['wirid'][$wStatus]++;
                    $row['summary']['wirid']['total']++;
                }
                if ($yStatus) {
                    $row['summary']['yasinan'][$yStatus]++;
                    $row['summary']['yasinan']['total']++;
                }
            }

            $rekapData[] = $row;
        }

        return view('mahadiyah.rekap-absensi-pengurus', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));
    }

    public function downloadRekapAbsensiPengurus(Request $request)
    {
        try {
            $startDate = $request->input('start_date')
                ? Carbon::parse($request->start_date)
                : Carbon::now()->startOfWeek();

            $endDate = $request->input('end_date')
                ? Carbon::parse($request->end_date)
                : Carbon::now()->endOfWeek();

            $dates = [];
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dates[] = $currentDate->format('d-m-Y');
                $currentDate->addDay();
            }

            $totalDays = count($dates);

            $pengurusList = Pengurus::with(['jabatan.divisi'])
                ->get()
                ->sortBy(function($p) {
                    $tipe = $p->jabatan->divisi->tipe ?? 'z_none';
                    $divName = $p->jabatan->divisi->nama ?? 'z_none';
                    $jabName = $p->jabatan->nama ?? 'z_none';
                    $tipeOrder = $tipe === 'kepkam' ? 0 : 1;
                    return sprintf('%d_%s_%s_%s', $tipeOrder, $divName, $jabName, $p->nama);
                });

            $nisList = $pengurusList->pluck('nis')->toArray();

            $bandonganData = DB::table('bandongan')
                ->whereIn('tanggal', $dates)
                ->whereIn('nis', $nisList)
                ->get()
                ->groupBy('nis');

            $wiridData = DB::table('wirid')
                ->whereIn('tanggal', $dates)
                ->whereIn('nis', $nisList)
                ->get()
                ->groupBy('nis');

            $yasinanData = DB::table('yasinan')
                ->whereIn('tanggal', $dates)
                ->whereIn('nis', $nisList)
                ->get()
                ->groupBy('nis');

            $rekapData = [];
            foreach ($pengurusList as $p) {
                $nis = $p->nis;
                $div = $p->jabatan->divisi ?? null;
                $tipe = $div ? $div->tipe : 'non';

                $row = [
                    'nis' => $p->nis,
                    'nama' => $p->nama,
                    'jabatan' => $p->jabatan->nama ?? 'Pengurus',
                    'divisi' => $div->nama ?? 'Tanpa Divisi',
                    'tipe' => $tipe,
                    'attendance' => [],
                    'summary' => [
                        'bandongan' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                        'wirid' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                        'yasinan' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                    ]
                ];

                $pBandongan = isset($bandonganData[$nis]) ? $bandonganData[$nis]->keyBy('tanggal') : collect();
                $pWirid = isset($wiridData[$nis]) ? $wiridData[$nis]->keyBy('tanggal') : collect();
                $pYasinan = isset($yasinanData[$nis]) ? $yasinanData[$nis]->keyBy('tanggal') : collect();

                foreach ($dates as $date) {
                    $bStatus = $pBandongan->get($date)->status ?? null;
                    $wStatus = $pWirid->get($date)->status ?? null;
                    $yStatus = ($tipe === 'kepkam') ? null : ($pYasinan->get($date)->status ?? null);

                    $row['attendance'][$date] = [
                        'bandongan' => $bStatus,
                        'wirid' => $wStatus,
                        'yasinan' => $yStatus,
                    ];

                    if ($bStatus) {
                        $row['summary']['bandongan'][$bStatus]++;
                        $row['summary']['bandongan']['total']++;
                    }
                    if ($wStatus) {
                        $row['summary']['wirid'][$wStatus]++;
                        $row['summary']['wirid']['total']++;
                    }
                    if ($yStatus) {
                        $row['summary']['yasinan'][$yStatus]++;
                        $row['summary']['yasinan']['total']++;
                    }
                }

                $rekapData[] = $row;
            }

            $pdf = Pdf::loadView('mahadiyah.rekap-absensi-pengurus-pdf', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));
            $pdf->setPaper('a4', 'landscape');

            $filename = 'Rekap_Absensi_Pengurus_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('[PDF Download] Error generating PDF for Pengurus', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function csvRekapAbsensiPengurus(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfWeek();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now()->endOfWeek();

        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-m-Y');
            $currentDate->addDay();
        }

        $pengurusList = Pengurus::with(['jabatan.divisi'])
            ->get()
            ->sortBy(function($p) {
                $tipe = $p->jabatan->divisi->tipe ?? 'z_none';
                $divName = $p->jabatan->divisi->nama ?? 'z_none';
                $jabName = $p->jabatan->nama ?? 'z_none';
                $tipeOrder = $tipe === 'kepkam' ? 0 : 1;
                return sprintf('%d_%s_%s_%s', $tipeOrder, $divName, $jabName, $p->nama);
            });

        $nisList = $pengurusList->pluck('nis')->toArray();

        $bandonganData = DB::table('bandongan')
            ->whereIn('tanggal', $dates)
            ->whereIn('nis', $nisList)
            ->get()
            ->groupBy('nis');

        $wiridData = DB::table('wirid')
            ->whereIn('tanggal', $dates)
            ->whereIn('nis', $nisList)
            ->get()
            ->groupBy('nis');

        $yasinanData = DB::table('yasinan')
            ->whereIn('tanggal', $dates)
            ->whereIn('nis', $nisList)
            ->get()
            ->groupBy('nis');

        $filename = 'Rekap_Absensi_Pengurus_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($pengurusList, $dates, $bandonganData, $wiridData, $yasinanData) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper excel opening
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            $headerRow = ['Nama', 'Jabatan', 'Divisi', 'Tipe'];
            foreach ($dates as $date) {
                $headerRow[] = $date;
            }
            $headerRow[] = 'Total Hadir Bandongan';
            $headerRow[] = 'Total Hadir Wirid';
            $headerRow[] = 'Total Hadir Yasinan';

            fputcsv($file, $headerRow, ';');

            foreach ($pengurusList as $p) {
                $nis = $p->nis;
                $div = $p->jabatan->divisi ?? null;
                $tipe = $div ? $div->tipe : 'non';

                $pBandongan = isset($bandonganData[$nis]) ? $bandonganData[$nis]->keyBy('tanggal') : collect();
                $pWirid = isset($wiridData[$nis]) ? $wiridData[$nis]->keyBy('tanggal') : collect();
                $pYasinan = isset($yasinanData[$nis]) ? $yasinanData[$nis]->keyBy('tanggal') : collect();

                $row = [
                    $p->nama,
                    $p->jabatan->nama ?? 'Pengurus',
                    $div->nama ?? 'Tanpa Divisi',
                    $tipe === 'kepkam' ? 'Kepala Kamar' : 'Non Kepala Kamar'
                ];

                $sumB = 0; $totalB = 0;
                $sumW = 0; $totalW = 0;
                $sumY = 0; $totalY = 0;

                foreach ($dates as $date) {
                    $bStatus = $pBandongan->get($date)->status ?? null;
                    $wStatus = $pWirid->get($date)->status ?? null;
                    $yStatus = ($tipe === 'kepkam') ? null : ($pYasinan->get($date)->status ?? null);

                    $parts = [];
                    if ($bStatus) {
                        $parts[] = "B:" . $bStatus;
                        if ($bStatus === 'H') $sumB++;
                        $totalB++;
                    }
                    if ($wStatus) {
                        $parts[] = "W:" . $wStatus;
                        if ($wStatus === 'H') $sumW++;
                        $totalW++;
                    }
                    if ($yStatus) {
                        $parts[] = "Y:" . $yStatus;
                        if ($yStatus === 'H') $sumY++;
                        $totalY++;
                    }

                    $row[] = empty($parts) ? '-' : implode(' | ', $parts);
                }

                $row[] = "{$sumB}/{$totalB}";
                $row[] = "{$sumW}/{$totalW}";
                $row[] = ($tipe === 'kepkam') ? '-' : "{$sumY}/{$totalY}";

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
