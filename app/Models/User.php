<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\MissingPerson;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'otp_code',
        'otp_expire_time',
        'location_id',
        'national_id_front_image',
        'national_id_back_image',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function missingPeople ()
    {
        return $this
        ->belongsToMany(MissingPerson::class , 'missing_person_user' , 'searcher_id' , 'missing_person_id')
        ->withTimestamps()
        ->with('location');
    }

    public function foundedPeople ()
    {
        return $this
        ->hasMany(FoundedPerson::class , 'founder_id' , 'id')
        ->with(['location' , 'policeStation']);
    }

    public function location()
    {
        return $this
        ->belongsTo(Location::class ,'location_id', 'id');
    }

    public function generateOtpCode ()
    {
        $this->timestamps = false ;
        $this->otp_code = rand(1000 , 9999);
        $this->otp_expire_time = now()->addMinutes(10);
        $this->save();
    } 


    public function getProfileImageAttribute ($value)
    {
        return '\images\user\profile_images\\'.$value;
    }

    public function getNationalIdFrontImageAttribute ($value)
    {
        return '\images\user\national_id_front_images\\'.$value;
    }

    public function getNationalIdBackImageAttribute ($value)
    {
        return '\images\user\national_id_back_images\\'.$value;
    }
}
