<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;
    protected $table = 'kegiatan';
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
}
