<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Order;
use App\Models\Like;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'name_kanji',
        'name_kana',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 購入（orders）とのリレーション
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // いいね（likes）とのリレーション
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}

