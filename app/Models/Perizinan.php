<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Perizinan extends Model
{
    use HasFactory;
    protected $table = 'perizinan';
    protected $guarded = [];
    protected $primaryKey = 'nis';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
    public function alasanizin()
    {
        return $this->belongsTo(AlasanIzin::class, 'alasan', 'id');
    }
    public function statusizin()
    {
        return $this->belongsTo(StatusIzin::class, 'status', 'id');
    }
}
