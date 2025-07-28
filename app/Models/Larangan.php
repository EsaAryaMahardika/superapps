<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Larangan extends Model
{
    use HasFactory;
    protected $table = 'larangan';
    protected $guarded = [];
    public $incrementing = false;
    public $timestamps = false;
}
