<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';
    protected $fillable = ['asrama_id', 'nama', 'kepkam_nis'];

    public function asrama()
    {
        return $this->belongsTo(Asrama::class, 'asrama_id', 'id');
    }

    public function kepkam()
    {
        return $this->belongsTo(Pengurus::class, 'kepkam_nis', 'nis');
    }

    public function santri()
    {
        return $this->hasMany(Santri::class, 'kepkam', 'kepkam_nis');
    }
}
