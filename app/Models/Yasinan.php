<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Yasinan extends Model
{
    protected $table = 'yasinan';
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
