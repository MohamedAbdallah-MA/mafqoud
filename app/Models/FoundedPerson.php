<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundedPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "image",
        "gender",
        "description",
        "location_id",
        "founder_id",
        "police_station_id",
        "founded_at",
    ];

    public function founder()
    {
        return $this->belongsTo(User::class , 'founder_id' , 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class , 'location_id' , 'id');
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class , 'police_station_id' , 'id');
    }

    public function getImageAttribute($value)
    {
        return '\images\founded_people\\'.$value ;
    }
}
