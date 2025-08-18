<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;
    protected $table = 'santri';
    protected $guarded = [];
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'nis';
    protected $keyType = 'string';
    protected $casts = [
        'nis' => 'string'
    ];
    // public function asrama()
    // {
    //     return $this->belongsTo(Asrama::class, 'asr_id', 'id');
    // }
    // public function jenjang()
    // {
    //     return $this->belongsTo(Jenjang::class, 'jen_id', 'id');
    // }
    // public function prov()
    // {
    //     return $this->belongsTo(Provinsi::class, 'prov_id', 'id');
    // }
    // public function kab()
    // {
    //     return $this->belongsTo(Kabupaten::class, 'kab_id', 'id');
    // }
    // public function kec()
    // {
    //     return $this->belongsTo(Kecamatan::class, 'kec_id', 'id');
    // }
    // public function kel()
    // {
    //     return $this->belongsTo(Kelurahan::class, 'kec_id', 'id');
    // }
    // public function kelas()
    // {
    //     return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    // }
}
