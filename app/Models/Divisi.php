<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';
    protected $guarded = [];

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'divisi_id', 'id');
    }

    public function pengurus()
    {
        return $this->hasManyThrough(Pengurus::class, Jabatan::class, 'divisi_id', 'jabatan_id');
    }
}
