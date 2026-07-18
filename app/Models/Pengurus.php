<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    use HasFactory;

    protected $table = 'pengurus';
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'nis' => 'string'
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    public function divisi()
    {
        // ponytail: chain jabatan->divisi lebih simpel, relasi ini tidak dipakai langsung di codebase
        return $this->hasOneThrough(Divisi::class, Jabatan::class, 'id', 'id', 'jabatan_id', 'divisi_id');
    }
}
