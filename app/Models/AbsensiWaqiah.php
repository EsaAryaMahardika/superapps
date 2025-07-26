<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiWaqiah extends Model
{
    use HasFactory;
    protected $table = 'absen_waqiah';
    protected $fillable = ['nis', 'status'];
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
}
