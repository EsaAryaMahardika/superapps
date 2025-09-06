<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'user';
    public $timestamps = false;
    protected $guarded = [
        'username',
        'password',
        'role'
    ];
    protected $hidden = [
        'password'
    ];
     protected function casts(): array
    {
        return [
            'password' => 'hashed'
        ];
    }
    public function santri(){
        return $this->belongsTo(Santri::class, 'username', 'kepkam');
    }
    public function pengurus(){
        return $this->belongsTo(Pengurus::class, 'username', 'nis');
    }
}
