<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliceStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' ,
        'location_id' ,
    ];

    public function location ()
    {
        return $this->belongsTo(Location::class , 'location_id' , 'id');
    }

    public function foundedPeople ()
    {
        return $this->hasMany(FoundedPerson::class , 'police_station_id' , 'id');
    }
}
