<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kegiatan;
use App\Models\Larangan;
use App\Models\AbsensiNgaji;
use Illuminate\Http\Request;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiMingguan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class KepalaKamar extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function dashboard()
    {
        // Generate last 7 days
        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $dates->push(Carbon::now()->subDays($i)->format('d/m/Y'));
        }

        $absen_kegiatan = function ($model, $kegiatan, $value) use ($dates) {
            $modelClass = "\\App\\Models\\$model";
            $data = $modelClass::select(
                'tanggal',
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
            )
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                })
                ->where($kegiatan, $value)
                ->groupBy('tanggal')
                ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') desc")
                ->limit(7)
                ->get()
                ->keyBy('tanggal');

            // Map over the last 7 days and fill missing data
            return $dates->map(function ($date) use ($data) {
                if ($data->has($date)) {
                    $item = $data->get($date);
                    $item->is_filled = true;
                    return $item;
                } else {
                    return (object) [
                        'tanggal' => $date,
                        'hadir' => '-',
                        'sakit' => '-',
                        'izin' => '-',
                        'alfa' => '-',
                        'is_filled' => false
                    ];
                }
            });
        };

        $subuh = $absen_kegiatan('AbsensiJamaah', 'sholat', 2);
        $dhuhur = $absen_kegiatan('AbsensiJamaah', 'sholat', 3);
        $ashar = $absen_kegiatan('AbsensiJamaah', 'sholat', 4);
        $maghrib = $absen_kegiatan('AbsensiJamaah', 'sholat', 5);
        $isya = $absen_kegiatan('AbsensiJamaah', 'sholat', 6);
        $ngasore = $absen_kegiatan('AbsensiNgaji', 'ngaji', 10);
        $ngamalam = $absen_kegiatan('AbsensiNgaji', 'ngaji', 11);

        $waqiahData = AbsensiWaqiah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
            ->whereHas('santri', function ($q) {
                $q->where('kepkam', $this->user->username);
            })
            ->groupBy('tanggal')
            ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') desc")
            ->limit(7)
            ->get()
            ->keyBy('tanggal');

        $waqiah = $dates->map(function ($date) use ($waqiahData) {
            if ($waqiahData->has($date)) {
                $item = $waqiahData->get($date);
                $item->is_filled = true;
                return $item;
            } else {
                return (object) [
                    'tanggal' => $date,
                    'hadir' => '-',
                    'sakit' => '-',
                    'izin' => '-',
                    'alfa' => '-',
                    'is_filled' => false
                ];
            }
        });

        return view('kepkam.dashboard', compact(
            'waqiah',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
            'ngasore',
            'ngamalam'
        ));
    }
    // ---------------- //
    // ABSENSI KEGIATAN //
    // ---------------- //
    public function absensi()
    {
        $today = Carbon::now()->format('d/m/Y');

        // Define all activities configuration
        // ID corresponds to the value used in the form select and match expression
        $activities = [
            ['id' => '1', 'title' => 'Absensi Waqiah', 'model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['id' => '2', 'title' => 'Absensi Subuh', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => '3', 'title' => 'Absensi Dhuhur', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => '4', 'title' => 'Absensi Ashar', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => '5', 'title' => 'Absensi Maghrib', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => '6', 'title' => 'Absensi Isya', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => '10', 'title' => 'Absensi Ngaji Sore', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['id' => '11', 'title' => 'Absensi Ngaji Malam', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $completed = [];
        $pending = [];

        foreach ($activities as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $today)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                })
                ->with('santri');

            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }

            $data = $query->get();

            if ($data->isNotEmpty()) {
                // Prepare mapped statuses for easier consumption by Edit JS
                $statuses = [];
                foreach ($data as $d) {
                    $statuses[$d->nis] = $d->status;
                }

                $completed[] = [
                    'id' => $act['id'],
                    'title' => $act['title'],
                    'data' => $data,
                    'statuses' => $statuses
                ];
            } else {
                $pending[] = [
                    'id' => $act['id'],
                    'title' => $act['title']
                ];
            }
        }

        $kegiatan = Kegiatan::where('ket', 'S')->get();
        $santri = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();

        $completedIds = array_column($completed, 'id');

        return view('kepkam.absensi', compact('completed', 'pending', 'kegiatan', 'santri', 'today', 'activities', 'completedIds'));
    }
    public function absen(Request $request)
    {
        $validIds = ['1', '2', '3', '4', '5', '6', '10', '11'];

        $request->validate([
            'tanggal'                => 'required|date',
            'kegiatan'               => 'required|in:' . implode(',', $validIds),
            'santri'                 => 'required|array|min:1',
            'santri.*'               => 'required|in:H,S,I,A',
            'additional_activities'  => 'nullable|array',
            'additional_activities.*'=> 'in:' . implode(',', $validIds),
        ]);

        $tanggal = Carbon::parse($request->tanggal)->format('d/m/Y');

        $targetActivities = array_unique(array_merge(
            [$request->kegiatan],
            $request->input('additional_activities', [])
        ));

        foreach ($targetActivities as $actId) {
            foreach ($request->santri as $nis => $status) {
                match ($actId) {
                    '1' => AbsensiWaqiah::updateOrCreate(
                        ['nis' => (string) $nis, 'tanggal' => $tanggal],
                        ['status' => $status]
                    ),
                    '2', '3', '4', '5', '6' => AbsensiJamaah::updateOrCreate(
                        ['nis' => (string) $nis, 'tanggal' => $tanggal, 'sholat' => $actId],
                        ['status' => $status]
                    ),
                    '10', '11' => AbsensiNgaji::updateOrCreate(
                        ['nis' => (string) $nis, 'tanggal' => $tanggal, 'ngaji' => $actId],
                        ['status' => $status]
                    ),
                };
            }
        }

        $msg = count($targetActivities) > 1
            ? 'Absensi berhasil disimpan untuk ' . count($targetActivities) . ' kegiatan'
            : 'Absensi berhasil disimpan';
        session()->flash('success', $msg);
        return redirect('/kepkam/absensi');
    }

    public function checkCompleted(Request $request)
    {
        // Convert Y-m-d to d/m/Y format
        $date = Carbon::parse($request->date)->format('d/m/Y');

        $activities = [
            ['id' => '1', 'model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['id' => '2', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => '3', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => '4', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => '5', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => '6', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => '10', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['id' => '11', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $completedIds = [];

        foreach ($activities as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $date)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                });

            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }

            if ($query->exists()) {
                $completedIds[] = $act['id'];
            }
        }

        return response()->json(['completedIds' => $completedIds]);
    }

    public function hapusAbsen($id)
    {
        $validIds = ['1', '2', '3', '4', '5', '6', '10', '11'];
        if (!in_array($id, $validIds)) abort(422);

        $today = Carbon::now()->format('d/m/Y');

        try {
            match ($id) {
                '1' => AbsensiWaqiah::where('tanggal', $today)
                    ->whereHas('santri', fn($q) => $q->where('kepkam', $this->user->username))
                    ->delete(),
                '2', '3', '4', '5', '6' => AbsensiJamaah::where('tanggal', $today)
                    ->where('sholat', $id)
                    ->whereHas('santri', fn($q) => $q->where('kepkam', $this->user->username))
                    ->delete(),
                '10', '11' => AbsensiNgaji::where('tanggal', $today)
                    ->where('ngaji', $id)
                    ->whereHas('santri', fn($q) => $q->where('kepkam', $this->user->username))
                    ->delete(),
            };

            session()->flash('success', 'Absensi berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus absensi');
        }

        return redirect('/kepkam/absensi');
    }
    private function activities(): array
    {
        return [
            ['id' => '2',  'name' => 'Subuh',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => '3',  'name' => 'Dhuhur',      'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => '4',  'name' => 'Ashar',        'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => '5',  'name' => 'Maghrib',      'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => '6',  'name' => 'Isya',         'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => '1',  'name' => 'Waqiah',       'model' => 'AbsensiWaqiah', 'col' => null,      'val' => null],
            ['id' => '10', 'name' => 'Ngaji Sore',   'model' => 'AbsensiNgaji',  'col' => 'ngaji',   'val' => 10],
            ['id' => '11', 'name' => 'Ngaji Malam',  'model' => 'AbsensiNgaji',  'col' => 'ngaji',   'val' => 11],
        ];
    }

    private function fetchAttendance(string $tanggal): array
    {
        $data = [];
        foreach ($this->activities() as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $tanggal)
                ->whereHas('santri', fn($q) => $q->where('kepkam', $this->user->username));
            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }
            $data[$act['name']] = $query->get()->keyBy('nis');
        }
        return $data;
    }
    public function mingguan()
    {
        $santri = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();
        $larangan = Larangan::select('id', 'nama')->where('ket', 'K')->get();
        $mingguan = AbsensiMingguan::whereHas('santri', function ($q) {
            $q->where('kepkam', $this->user->username);
        })->with('santri', 'larangan')->get();
        return view('kepkam.mingguan', compact('larangan', 'mingguan', 'santri'));
    }

    // ------------------- //
    // REKAPAN HARIAN      //
    // ------------------- //
    public function rekapHarian(Request $request)
    {
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->tanggal)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        $santriList     = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->orderBy('nama')->get();
        $activities     = $this->activities();
        $attendanceData = $this->fetchAttendance($tanggal);

        $rekapData = $santriList->map(fn($santri) => [
            'nis'        => $santri->nis,
            'nama'       => $santri->nama,
            'attendance' => collect($activities)->mapWithKeys(fn($act) => [
                $act['name'] => $attendanceData[$act['name']][$santri->nis]->status ?? '-'
            ])->all(),
        ])->all();

        $kepalaKamar = $this->user->pengurus->nama;
        return view('kepkam.rekap-harian', compact('rekapData', 'activities', 'tanggal', 'kepalaKamar'));
    }

    public function downloadRekapHarian(Request $request)
    {
        try {
            \Log::info('[PDF Download] Starting PDF generation', ['tanggal' => $request->tanggal]);

            // Get selected date or default to today
            $tanggal = $request->input('tanggal')
                ? Carbon::parse($request->tanggal)->format('d/m/Y')
                : Carbon::now()->format('d/m/Y');

            \Log::info('[PDF Download] Date:', ['tanggal' => $tanggal]);

            $santriList     = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->orderBy('nama')->get();
            $activities     = $this->activities();
            $attendanceData = $this->fetchAttendance($tanggal);

            \Log::info('[PDF Download] Santri count:', ['count' => $santriList->count()]);

            $rekapData = $santriList->values()->map(fn($santri, $index) => [
                'no'         => $index + 1,
                'nis'        => $santri->nis,
                'nama'       => $santri->nama,
                'attendance' => collect($activities)->mapWithKeys(fn($act) => [
                    $act['name'] => $attendanceData[$act['name']][$santri->nis]->status ?? '-'
                ])->all(),
            ])->all();

            // Get kepala kamar name with null check
            $kepalaKamar = 'N/A';
            if ($this->user && $this->user->pengurus) {
                $kepalaKamar = $this->user->pengurus->nama;
            } else {
                \Log::warning('[PDF Download] Pengurus relation is null for user', ['username' => $this->user->username]);
            }

            \Log::info('[PDF Download] Kepala Kamar:', ['nama' => $kepalaKamar]);

            // Validate data before PDF generation
            if (empty($rekapData)) {
                \Log::warning('[PDF Download] No data to generate PDF');
                return response()->json([
                    'error' => 'Tidak ada data santri untuk tanggal ini'
                ], 400);
            }

            \Log::info('[PDF Download] Loading PDF view');

            // Load PDF view
            $pdf = Pdf::loadView('kepkam.rekap-harian-pdf', compact('rekapData', 'activities', 'tanggal', 'kepalaKamar'));

            // Calculate dynamic page height based on data rows
            // DomPDF uses POINTS as unit (1 inch = 72 points)
            // Standard portrait width = 8.5 inches = 612 points

            // Base height estimation (header + footer + margins) ~5 inches = 360 points
            $baseHeightPoints = 360;

            // Each data row approximately 0.25 inches = 18 points
            $rowHeightPoints = 18;

            // Header table row ~0.4 inches = 29 points
            $headerRowPoints = 29;

            $rowCount = count($rekapData);
            $contentHeightPoints = $headerRowPoints + ($rowCount * $rowHeightPoints);
            $totalHeightPoints = $baseHeightPoints + $contentHeightPoints;

            // Set minimum height (8 inches = 576 points)
            $totalHeightPoints = max($totalHeightPoints, 576);

            \Log::info('[PDF Download] Page dimensions', [
                'width_points' => 612,
                'height_points' => $totalHeightPoints,
                'rows' => $rowCount,
                'width_inches' => 8.5,
                'height_inches' => round($totalHeightPoints / 72, 2)
            ]);

            // Set custom paper size in POINTS: [x1, y1, x2, y2]
            // Width: 612 points (8.5 inches)
            // Height: dynamic based on content
            $pdf->setPaper([0, 0, 612, $totalHeightPoints]);

            \Log::info('[PDF Download] PDF generated successfully');

            // Download PDF
            $filename = 'Rekap_Harian_' . str_replace('/', '-', $tanggal) . '.pdf';
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

    public function i_mingguan(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date_format:d-m-Y',
            'larangan'   => 'required|string|max:255',
            'santri'     => 'required|array|min:1',
            'santri.*'   => 'required|string|exists:santri,nis',
        ]);

        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('d/m/Y');
        foreach ($request->santri as $nis) {
            AbsensiMingguan::create([
                'nis'         => (string) $nis,
                'larangan_id' => $request->larangan,
                'tanggal'     => $tanggal,
            ]);
        }
        session()->flash('success', 'Absensi berhasil disimpan');
        return redirect('/kepkam/mingguan');
    }
}
