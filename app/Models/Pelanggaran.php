<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    use HasFactory;
    protected $table = 'pelanggaran';
    protected $fillable = ['nis', 'langgar_id', 'hukuman'];
    protected $guarded = ['tanggal'];
    protected $primaryKey = 'nis';
    public $incrementing = false;
    public $timestamps = false;
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'nis', 'nis');
    }
    public function larangan()
    {
        return $this->belongsTo(Larangan::class, 'langgar_id', 'id');
    }
}
