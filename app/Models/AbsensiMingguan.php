<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiMingguan extends Model
{
    protected $table = 'absen_mingguan';
    protected $guarded = [];
    public $timestamps = false;
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
    public function larangan()
    {
        return $this->belongsTo(Larangan::class, 'pelanggaran', 'id');
    }
}
