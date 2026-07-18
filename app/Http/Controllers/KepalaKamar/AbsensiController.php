<?php

namespace App\Http\Controllers\KepalaKamar;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kegiatan;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiNgaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AbsensiController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    private function activities(): array
    {
        return [
            ['id' => '1',  'title' => 'Absensi Waqiah',      'model' => 'AbsensiWaqiah', 'col' => null,    'val' => null],
            ['id' => '2',  'title' => 'Absensi Subuh',        'model' => 'AbsensiJamaah', 'col' => 'sholat','val' => 2],
            ['id' => '3',  'title' => 'Absensi Dhuhur',       'model' => 'AbsensiJamaah', 'col' => 'sholat','val' => 3],
            ['id' => '4',  'title' => 'Absensi Ashar',        'model' => 'AbsensiJamaah', 'col' => 'sholat','val' => 4],
            ['id' => '5',  'title' => 'Absensi Maghrib',      'model' => 'AbsensiJamaah', 'col' => 'sholat','val' => 5],
            ['id' => '6',  'title' => 'Absensi Isya',         'model' => 'AbsensiJamaah', 'col' => 'sholat','val' => 6],
            ['id' => '10', 'title' => 'Absensi Ngaji Sore',   'model' => 'AbsensiNgaji',  'col' => 'ngaji', 'val' => 10],
            ['id' => '11', 'title' => 'Absensi Ngaji Malam',  'model' => 'AbsensiNgaji',  'col' => 'ngaji', 'val' => 11],
        ];
    }

    public function index()
    {
        $today      = Carbon::now()->format('d/m/Y');
        $activities = $this->activities();
        $completed  = [];
        $pending    = [];

        foreach ($activities as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $today)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                })
                ->with('santri');

            if ($act['col']) $query->where($act['col'], $act['val']);

            $data = $query->get();

            if ($data->isNotEmpty()) {
                $statuses = [];
                foreach ($data as $d) $statuses[$d->nis] = $d->status;
                $completed[] = ['id' => $act['id'], 'title' => $act['title'], 'data' => $data, 'statuses' => $statuses];
            } else {
                $pending[] = ['id' => $act['id'], 'title' => $act['title']];
            }
        }

        $kegiatan    = Kegiatan::where('ket', 'S')->get();
        $santri      = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();
        $completedIds = array_column($completed, 'id');

        return view('kepkam.absensi', compact('completed', 'pending', 'kegiatan', 'santri', 'today', 'activities', 'completedIds'));
    }

    public function store(Request $request)
    {
        $validIds = ['1', '2', '3', '4', '5', '6', '10', '11'];

        $request->validate([
            'tanggal'                 => 'required|date',
            'kegiatan'                => 'required|in:' . implode(',', $validIds),
            'santri'                  => 'required|array|min:1',
            'santri.*'                => 'required|in:H,S,I,A',
            'additional_activities'   => 'nullable|array',
            'additional_activities.*' => 'in:' . implode(',', $validIds),
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
        $date       = Carbon::parse($request->date)->format('d/m/Y');
        $activities = $this->activities();
        $completedIds = [];

        foreach ($activities as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $date)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                });
            if ($act['col']) $query->where($act['col'], $act['val']);
            if ($query->exists()) $completedIds[] = $act['id'];
        }

        return response()->json(['completedIds' => $completedIds]);
    }

    public function destroy($id)
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
}
