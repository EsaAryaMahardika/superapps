<?php

namespace App\Http\Controllers\Mahadiyah;

use Carbon\Carbon;
use App\Models\Wirid;
use App\Models\Yasinan;
use App\Models\Kegiatan;
use App\Models\Pengurus;
use App\Models\Bandongan;
use App\Models\Divisi;
use App\Models\LiburPengurus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbsensiController extends Controller
{
    public function index()
    {
        $with      = ['pengurus.jabatan.divisi'];
        $bandongan = Bandongan::with($with)->get();
        $wirid     = Wirid::with($with)->get();
        $yasinan   = Yasinan::with($with)->get();

        $divisiNon    = Divisi::where('tipe', 'non')->with(['jabatan.pengurus'])->orderBy('id')->get();
        $divisiKepkam = Divisi::where('tipe', 'kepkam')->with(['jabatan.pengurus'])->orderBy('id')->get();

        $totalSemua = Pengurus::count();
        $totalNon   = Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'non'))->count();

        // Ambil semua data libur, group by tanggal
        $semuaLibur = LiburPengurus::orderBy('tanggal')->get()
            ->groupBy('tanggal')
            ->map(fn($rows) => $rows->pluck('keterangan', 'tipe'));

        return view('mahadiyah.absensi-pengurus', compact(
            'bandongan', 'wirid', 'yasinan',
            'divisiNon', 'divisiKepkam', 'totalSemua', 'totalNon',
            'semuaLibur'
        ));
    }

    public function edit($tipe, $tanggal)
    {
        $tanggalDb  = $tanggal;
        $modelClass = match ($tipe) {
            'bandongan' => Bandongan::class,
            'wirid'     => Wirid::class,
            'yasinan'   => Yasinan::class,
            default     => abort(404),
        };

        $existing = $modelClass::with('pengurus.jabatan.divisi')
            ->where('tanggal', $tanggalDb)->get()->keyBy('nis');

        $pengurus = Pengurus::with('jabatan.divisi')
            ->whereIn('nis', $existing->keys())->orderBy('nama')->get();

        $judulMap       = ['bandongan' => 'Bandongan', 'wirid' => 'Wirid', 'yasinan' => 'Yasinan'];
        $judul          = $judulMap[$tipe];
        $tanggalDisplay = Carbon::createFromFormat('d-m-Y', $tanggalDb);

        return view('mahadiyah.edit-absensi', compact(
            'tipe', 'tanggal', 'tanggalDb', 'tanggalDisplay', 'existing', 'pengurus', 'judul'
        ));
    }

    public function update(Request $request, $tipe, $tanggal)
    {
        $request->validate([
            'pengurus'   => 'required|array',
            'pengurus.*' => 'required|in:H,S,I,A',
        ]);

        $tanggalDb  = $tanggal;
        $judulMap   = ['bandongan' => 'Bandongan', 'wirid' => 'Wirid', 'yasinan' => 'Yasinan'];
        $judul      = $judulMap[$tipe] ?? $tipe;
        $modelClass = match ($tipe) {
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

    public function create()
    {
        $kegiatan      = Kegiatan::where('ket', 'P')->get();
        $pengurusSemua = Pengurus::with('jabatan.divisi')->orderBy('nama')->get();
        $pengurusNon   = Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'non'))
            ->with('jabatan.divisi')->orderBy('nama')->get();

        // Kirim data libur ke form create
        $semuaLibur = LiburPengurus::orderBy('tanggal')->get()
            ->groupBy('tanggal')
            ->map(fn($rows) => $rows->pluck('keterangan', 'tipe'));

        return view('mahadiyah.create-absensi', compact('kegiatan', 'pengurusSemua', 'pengurusNon', 'semuaLibur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan'   => 'required|in:7,8,9',
            'tanggal'    => 'required|string',
            'pengurus'   => 'required|array|min:1',
            'pengurus.*' => 'required|in:H,S,I,A',
        ]);

        $kegiatan   = $request->kegiatan;
        $tanggal    = $request->tanggal;
        $modelClass = match ($kegiatan) {
            '7' => Bandongan::class,
            '8' => Wirid::class,
            '9' => Yasinan::class,
        };

        if ($modelClass::where('tanggal', $tanggal)->exists()) {
            session()->flash('error', 'Absensi tanggal ini sudah dibuat');
            return redirect('/mahadiyah/absensi-pengurus');
        }

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

    // ------------------- //
    // LIBUR PENGURUS       //
    // ------------------- //

    public function liburStore(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|string|regex:/^\d{2}-\d{2}-\d{4}$/',
            'tipe'       => 'required|in:bandongan,wirid,yasinan',
            'keterangan' => 'nullable|string|max:255',
        ]);

        LiburPengurus::updateOrCreate(
            ['tanggal' => $request->tanggal, 'tipe' => $request->tipe],
            ['keterangan' => $request->keterangan]
        );

        session()->flash('success', ucfirst($request->tipe) . ' pada ' . $request->tanggal . ' ditandai libur.');
        return redirect('/mahadiyah/absensi-pengurus');
    }

    public function liburDestroy(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|string',
            'tipe'    => 'required|in:bandongan,wirid,yasinan',
        ]);

        LiburPengurus::where('tanggal', $request->tanggal)
            ->where('tipe', $request->tipe)
            ->delete();

        session()->flash('success', ucfirst($request->tipe) . ' pada ' . $request->tanggal . ' libur dibatalkan.');
        return redirect('/mahadiyah/absensi-pengurus');
    }

    public function mingguan()
    {
        return view('mahadiyah.absen-mingguan');
    }

    public function kegiatan()
    {
        // placeholder — belum diimplementasi
    }
}
