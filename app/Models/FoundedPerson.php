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
        "searcher_id",
        "founded_at",
    ];

    public function searcher()
    {
        return $this->belongsTo(User::class , 'searcher_id' , 'id');
    }

    public function getImageAttribure($value)
    {
        return '\images\founded_people\\'.$value ;
    }
}
