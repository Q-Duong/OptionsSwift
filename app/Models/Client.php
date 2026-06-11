<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use Notifiable;

    protected $guard = 'client'; // Khai báo guard

    protected $fillable = [
        'name', 'email', 'password', 'is_approved',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
