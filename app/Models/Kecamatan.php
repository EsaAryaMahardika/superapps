<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kecamatan extends Model
{
    use HasFactory;
    protected $table = 'kec';
    protected $guarded = [];
    public function kab()
    {
        return $this->belongsTo(Kabupaten::class, 'kab_id', 'id');
    }
}
