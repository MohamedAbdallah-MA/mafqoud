<?php
namespace App\Http\Repositories;

use App\Models\User;
use App\Http\Traits\ImageTrait;
use App\Rules\StrongPasswordRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Interfaces\AuthInterface;
use Illuminate\Support\Facades\Validator;

class AuthRepository implements AuthInterface {

    use ImageTrait ;
    use ApiResponseTrait ;

    public function register ($request){

        //* validate the request 
        $validation = Validator::make($request->all() ,[
            'name'                      =>  'required' ,
            'phone'                     =>  'required | unique:users,phone' ,
            'password'                  =>  ['required' , new StrongPasswordRule] ,
            'national_id_front_image'   =>  'required | image | mimes:jpeg,png,jpg | max:2048' ,
            'national_id_back_image'    =>  'required  | image | mimes:jpeg,png,jpg | max:2048' ,
            'profile_image'             =>  'required | image | mimes:jpeg,png,jpg | max:2048' ,
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse('422' , 'validation error' , $validation->errors());
        }
        
        //* set image's names
        $nationalIdFrontImageName = $this->setImageName($request->national_id_front_image , 'national_id_front_image');
        $nationalIdBackImageName = $this->setImageName($request->national_id_back_image , 'national_id_back_image');
        $profileImageName = $this->setImageName($request->profile_image , 'personal_image');
        
        //* upload images to server   
        $this->uploadImage($request->national_id_front_image , $nationalIdFrontImageName , 'user\national_id_front_images');
        $this->uploadImage($request->national_id_back_image , $nationalIdBackImageName , 'user\national_id_back_images');
        $this->uploadImage($request->profile_image , $profileImageName , 'user\profile_images');

        //* insert user data into db
        User::create([
            'name'                      =>  $request->name ,
            'phone'                     =>  $request->phone ,
            'password'                  =>  Hash::make($request->password) ,
            'national_id_front_image'   =>  $nationalIdFrontImageName ,
            'national_id_back_image'    =>  $nationalIdBackImageName ,
            'profile_image'             =>  $profileImageName ,
        ]);

        return $this->apiResponse('200' , 'successfully registered' , $validation->errors());

    }

    public function login ($request)
    {
        $validations = Validator::make($request->all() , [
            'phone' => 'required' ,
            'password' => ['required' , new StrongPasswordRule] ,
        ]);

        if ($validations->fails())
        {
            return $this->apiResponse(422 , 'validation error' ,$validations->errors()) ;
        }

        $credentials = $request->only('phone' , 'password');
        if (! $token = Auth::attempt($credentials))
        {
            return $this->apiResponse(401 , 'unauthorized');
        }

        return $this->respondWithToken($token) ;

    }

    protected function respondWithToken($token)
    {
        $array = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60 
        ];

        return $this->apiResponse( 200 , 'successful login' , null , $array );
    }
}