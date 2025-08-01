<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kabupaten extends Model
{
    use HasFactory;
    protected $table = 'kab';
    protected $guarded = [];
    public function prov()
    {
        return $this->belongsTo(Provinsi::class, 'prov_id', 'id');
    }
}
