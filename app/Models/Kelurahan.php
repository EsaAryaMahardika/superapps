<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelurahan extends Model
{
    use HasFactory;
    protected $table = 'kel';
    protected $guarded = [];
    public function kec()
    {
        return $this->belongsTo(Kecamatan::class, 'kec_id', 'id');
    }
}
