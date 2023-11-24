<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        "country",
        "state",
        "city",
    ];


    public function missingPeople()
    {
        return $this->hasMany(MissingPerson::class , 'location_id' , 'id');
    }

    public function foundedPeople()
    {
        return $this->hasMany(FoundedPerson::class , 'location_id' , 'id');
    }
}
