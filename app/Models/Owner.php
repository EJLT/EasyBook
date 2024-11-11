<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = ['name', 'email', 'phone'];

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }
}
