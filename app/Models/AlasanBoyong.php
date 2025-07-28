<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlasanBoyong extends Model
{
    use HasFactory;
    protected $table = 'alasan_boyong';
    protected $guarded = [];
    public $timestamps = false;
}
