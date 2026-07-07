<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class Client extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use Billable;

    protected $guard = 'client';

    protected $fillable = [
        'name', 'email', 'password', 'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at', 'status'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
