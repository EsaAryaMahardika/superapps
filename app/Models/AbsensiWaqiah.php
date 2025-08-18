<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiWaqiah extends Model
{
    use HasFactory;
    protected $table = 'absen_waqiah';
    protected $guarded = [];
    public $incrementing = true;
    public $timestamps = false;
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
    protected $casts = [
        'nis' => 'string'
    ];
}
