<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boyong extends Model
{
    use HasFactory;
    protected $table = 'boyong';
    protected $guarded = [];
    protected $primaryKey = 'nis';
    public $incrementing = false;
    public $timestamps = false;
    public function kepkam() {
        return $this->belongsTo(Pengurus::class, 'kep_id', 'nis');
    }
    public function asrama() {
        return $this->belongsTo(Asrama::class, 'asr_id', 'id');
    }
    public function alasan() {
        return $this->belongsTo(AlasanBoyong::class, 'ala_id', 'id');
    }
    public function rencana() {
        return $this->belongsTo(Rencana::class, 'ren_id', 'id');
    }
}
