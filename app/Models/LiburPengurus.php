<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiburPengurus extends Model
{
    protected $table    = 'libur_pengurus';
    protected $fillable = ['tanggal', 'tipe', 'keterangan'];

    /**
     * Cek apakah kegiatan tipe tertentu libur pada tanggal tertentu.
     * Format tanggal: dd-mm-yyyy
     */
    public static function isLibur(string $tanggal, string $tipe): bool
    {
        return static::where('tanggal', $tanggal)->where('tipe', $tipe)->exists();
    }

    /**
     * Ambil semua libur pada tanggal tertentu, key by tipe.
     */
    public static function forTanggal(string $tanggal): \Illuminate\Support\Collection
    {
        return static::where('tanggal', $tanggal)->get()->keyBy('tipe');
    }

    /**
     * Ambil semua libur dalam range tanggal (array dd-mm-yyyy), group by tipe.
     */
    public static function forDateRange(array $dates): array
    {
        $rows = static::whereIn('tanggal', $dates)->get();
        $result = ['bandongan' => [], 'wirid' => [], 'yasinan' => []];
        foreach ($rows as $row) {
            $result[$row->tipe][] = $row->tanggal;
        }
        return $result;
    }
}
