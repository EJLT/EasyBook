<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'owner_id',
        'category_id',
    ];


    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
