<?php
namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\User;
use Twilio\Rest\Client;
use App\Models\Location;
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
    
    public function register ($request)
    {
        
        //* validate the request 
        $validation = Validator::make($request->all() ,[
            'name'                      => 'required | min:3 | max:50 ' ,
            'phone'                     => 'required | unique:users,phone' ,
            'gender'                    => 'required | in:male,female' ,
            'country'                   => 'required' ,
            'state'                     => 'required' ,
            'city'                      => 'required' ,
            'password'                  => ['required' , new StrongPasswordRule] ,
            'national_id_front_image'   => 'required | image | mimes:jpeg,png,jpg | max:2048' ,
            'national_id_back_image'    => 'required | image | mimes:jpeg,png,jpg | max:2048' ,
            'profile_image'             => 'required | image | mimes:jpeg,png,jpg | max:2048' ,
        ]);
        
        if ($validation->fails())
        {
            return $this->apiResponse('400' , 'validation error' , $validation->errors());
        }

        $location = Location::where([['country' , $request->country] , ['state' , $request->state] , ['city' , $request->city] ])->first();
        
        if ( is_null($location) )
        {
            $location = Location::create([
                'country'   => $request->country ,
                'state'     => $request->state ,
                'city'      => $request->city ,
            ]);
        }
        
        //* set image's names
        $nationalIdFrontImageName = $this->setImageName($request->national_id_front_image , 'national_id_front_image');
        $nationalIdBackImageName = $this->setImageName($request->national_id_back_image , 'national_id_back_image');
        $profileImageName = $this->setImageName($request->profile_image , 'personal_image');
        
        //* upload images to server   
        $this->uploadImage($request->national_id_front_image , $nationalIdFrontImageName , 'user/national_id_front_images');
        $this->uploadImage($request->national_id_back_image , $nationalIdBackImageName , 'user/national_id_back_images');
        $this->uploadImage($request->profile_image , $profileImageName , 'user/profile_images');
        //* insert user data into db
        User::create([
            'name'                      => $request->name ,
            'phone'                     => $request->phone ,
            'gender'                    => $request->gender,
            'password'                  => Hash::make($request->password) ,
            'location_id'               => $location->id ,
            'national_id_front_image'   => $nationalIdFrontImageName ,
            'national_id_back_image'    => $nationalIdBackImageName ,
            'profile_image'             => $profileImageName ,
        ]);

        return $this->apiResponse('200' , 'successfully registered' );

    }

    public function login ($request)
    {
        $validations = Validator::make($request->all() , [
            'phone'     => 'required | exists:users,phone' ,
            'password'  => ['required' , new StrongPasswordRule] ,
        ]);

        if ($validations->fails())
        {
            return $this->apiResponse(400 , 'validation error' ,$validations->errors()) ;
        }

        $credentials = $request->only('phone' , 'password');

        if (! $token = Auth::attempt($credentials))
        {
            return $this->apiResponse(401 , 'unauthorized' , "phone or password isn't correct");
        }

        return $this->respondWithToken($token) ;

    }

    protected function respondWithToken($token)
    {
        $array =
        [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60 
        ];

        return $this->apiResponse( 200 , 'successful login' , null , $array );
    }


    public function generateOtpCode ($request)
    {
        if($request->has('phone'))
        {
            $validation = Validator::make($request->all() , [
                'phone' => 'required | exists:users,phone'
            ]);

            if ($validation->fails())
            {
                return $this->apiResponse(400 , 'validation error' ,$validation->errors()) ;
            }

            $user = User::where('phone',$request->phone)->first();
        }
        elseif (!$request->has('phone'))
        {
            $user = User::where('id',Auth::user()->id)->first();
        }
        
        //* uncomment them to send otp code 
        // $user->generateOtpCode();
        // $account_sid = getenv('TWILIO_ACCOUNT_SID');
        // $auth_token = getenv('TWILIO_AUTH_TOKEN');
        // $twilio_number = getenv('TWILIO_NUMBER') ;
        // $client = new Client($account_sid, $auth_token);
        // $client->messages->create(
        // $user->phone,
        // array(
        //     'from' => $twilio_number,
        //     'body' => 'your otp is '.$user->otp_code
        // )
        // );
        return $this->apiResponse(200 , 'otp code generated successfully');
    }


    public function checkOtpCode ($request)
    {
        $validation = Validator::make($request->all() , [
            'otp_code' => 'required | digits:4 ',
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse('400' , 'validation error' , $validation->errors());
        }
        
        $user = User::where('id',Auth::user()->id)->first();
        
        if(now()->gt($user->otp_expire_time))
        {
            return $this->apiResponse('400' , 'otp cannot be used' , 'the otp code is expired' );
            
        }

        if ($request->otp_code == $user->otp_code)
        {
            $user->phone_verified_at = now();
            $user->save();
            return $this->apiResponse(200 , 'otp successfully verified');
        }
        return $this->apiResponse(400 , "otp isn't correct");

    }

    public function resetPassword ($request)
    {
        $validations = Validator::make($request->all() , [
            'phone'     => 'required | exists:users,phone' ,
            'password'  => ['required' , new StrongPasswordRule] ,
        ]);

        if ($validations->fails())
        {
            return $this->apiResponse('400' , 'validation error' , $validations->errors());
        }

        $user = User::where('phone' , $request->phone)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return $this->apiResponse(200 , 'password changed successfully');
    }
}