<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiNgaji extends Model
{
    protected $table = 'absen_ngaji';
    protected $guarded = [];
    public $timestamps = false;
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
    protected $casts = [
        'nis' => 'string'
    ];
}
