<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokSantri extends Model
{
    protected $table = 'kelompok_santri';
    protected $fillable = ['nama', 'kepkam_nis', 'keterangan'];

    public function santri()
    {
        return $this->hasMany(Santri::class, 'kelompok_id', 'id');
    }

    public function kepkam()
    {
        return $this->belongsTo(Pengurus::class, 'kepkam_nis', 'nis');
    }
}
