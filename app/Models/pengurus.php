<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    use HasFactory;
    protected $table = 'pengurus';
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey= 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'nis' => 'string'
    ];
}
