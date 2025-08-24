<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bandongan extends Model
{
    protected $table = 'bandongan';
    protected $fillable = ['nis', 'status', 'tanggal'];
    public $timestamps = false;
    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'nis', 'nis');
    }
    protected $casts = [
        'nis' => 'string'
    ];
}
