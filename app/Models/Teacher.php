<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Teacher extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'type',
        'age',
        'address',
        'profile_url'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}
