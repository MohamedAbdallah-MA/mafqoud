<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingPerson extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "image",
        "gender",
        "description",
        "location_id",
        "losted_at",
    ];

    public function location()
    {
        return $this
        ->belongsTo(Location::class ,'location_id', 'id');
    }

    public function searchers()
    {
        return $this
        ->belongsToMany(User::class , 'missing_person_user' , 'missing_person_id' , 'searcher_id')
        ->withTimestamps();
    }

    public function getImageAttribute($value)
    {
        return '\images\missing_people\\'.$value ;
    }
}
