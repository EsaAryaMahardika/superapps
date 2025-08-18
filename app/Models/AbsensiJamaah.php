<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiJamaah extends Model
{
    use HasFactory;
    protected $table = 'absen_jamaah';
    protected $guarded = [];
    public $incrementing = true;
    public $timestamps = false;
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
}
