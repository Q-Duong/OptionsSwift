<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    public $timestamps = true;

    protected $fillable = [
        'profile_firstname',
        'profile_lastname',
        'profile_phone',
        'profile_email',
        'profile_avatar',
        'date_of_birth',
        'profile_gender'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
