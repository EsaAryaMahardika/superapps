<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlasanIzin extends Model
{
    use HasFactory;
    protected $table = 'alasan_izin';
    protected $guarded = [];
    public $incrementing = false;
    public $timestamps = false;
}
