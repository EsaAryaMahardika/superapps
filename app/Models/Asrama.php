<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    use HasFactory;
    protected $table = 'asrama';
    protected $guarded = [];
    public $timestamps = false;

    public function kamar()
    {
        return $this->hasMany(Kamar::class, 'asrama_id', 'id');
    }
}

