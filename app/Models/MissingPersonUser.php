<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingPersonUser extends Model
{
    use HasFactory;
    protected $table = "missing_person_user" ;

    protected $fillable = [
        "missing_person_id",
        "searcher_id ",
    ];

}
