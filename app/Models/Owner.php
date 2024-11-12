<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Owner extends Authenticatable implements JWTSubject
{
    protected $fillable = ['name', 'email', 'phone', 'password'];

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    // Implementación de JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
