<?php

namespace App\Http\Controllers\KepalaKamar;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Larangan;
use App\Models\AbsensiMingguan;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiNgaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class RekapController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    private function activityConfig(): array
    {
        return [
            ['id' => '2',  'name' => 'Subuh',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => '3',  'name' => 'Dhuhur',      'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => '4',  'name' => 'Ashar',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => '5',  'name' => 'Maghrib',     'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => '6',  'name' => 'Isya',        'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => '1',  'name' => 'Waqiah',      'model' => 'AbsensiWaqiah', 'col' => null,      'val' => null],
            ['id' => '10', 'name' => 'Ngaji Sore',  'model' => 'AbsensiNgaji',  'col' => 'ngaji',   'val' => 10],
            ['id' => '11', 'name' => 'Ngaji Malam', 'model' => 'AbsensiNgaji',  'col' => 'ngaji',   'val' => 11],
        ];
    }

    private function fetchAttendance(string $tanggal): array
    {
        $data = [];
        foreach ($this->activityConfig() as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $tanggal)
                ->whereHas('santri', fn($q) => $q->where('kepkam', $this->user->username));
            if ($act['col']) $query->where($act['col'], $act['val']);
            $data[$act['name']] = $query->get()->keyBy('nis');
        }
        return $data;
    }

    public function mingguan()
    {
        $santri   = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();
        $larangan = Larangan::select('id', 'nama')->where('ket', 'K')->get();
        $mingguan = AbsensiMingguan::whereHas('santri', function ($q) {
            $q->where('kepkam', $this->user->username);
        })->with('santri', 'larangan')->get();

        return view('kepkam.mingguan', compact('larangan', 'mingguan', 'santri'));
    }

    public function storeMingguan(Request $request)
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

    public function rekapHarian(Request $request)
    {
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        $santriList  = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->orderBy('nama')->get();
        $attendanceByActivity = $this->fetchAttendance($tanggal);

        return view('kepkam.rekap-harian', compact('tanggal', 'santriList', 'attendanceByActivity'));
    }

    public function downloadRekapHarian(Request $request)
    {
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        try {
            $santriList = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->orderBy('nama')->get();
            $attendanceByActivity = $this->fetchAttendance($tanggal);

            // Calculate dynamic paper height
            $baseHeight      = 200;
            $rowHeight       = 25;
            $rowCount        = $santriList->count();
            $totalHeightPoints = $baseHeight + ($rowHeight * $rowCount);

            \Log::info('[PDF Download] Generating PDF', [
                'tanggal'        => $tanggal,
                'santri_count'   => $rowCount,
                'width_points'   => 612,
                'height_points'  => $totalHeightPoints,
                'rows'           => $rowCount,
                'width_inches'   => 8.5,
                'height_inches'  => round($totalHeightPoints / 72, 2),
            ]);

            $pdf = Pdf::loadView('kepkam.rekap-harian-pdf', compact('tanggal', 'santriList', 'attendanceByActivity'));
            $pdf->setPaper([0, 0, 612, $totalHeightPoints]);

            \Log::info('[PDF Download] PDF generated successfully');

            $filename = 'Rekap_Harian_' . str_replace('/', '-', $tanggal) . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('[PDF Download] Error generating PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }
}
