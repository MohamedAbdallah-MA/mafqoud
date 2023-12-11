<?php
namespace App\Http\Repositories;
use App\Models\User;
use App\Models\Location;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Interfaces\UserInterface;
use Illuminate\Support\Facades\Validator;

class UserRepository implements UserInterface {

    use ApiResponseTrait ;
    use ImageTrait;
    public function getUsers() 
    {
        $allUsers = [];
        $users = User::with(['missingPeople' , 'foundedPeople'])->get();
        foreach ($users as $user)
        {
            $userProfileImage = asset(public_path($user->profile_image));
            $userInformation = [
                'name'          => $user->name ,
                'phone'         => $user->phone ,
                'gender'        => $user->gender ,
                'country'       => $user->country ,
                'state'         => $user->state ,
                'city'          => $user->city ,
                'profile_image' => $userProfileImage ,
            ];
            
            $missingPeopleInformation = [];
            foreach ($user->missingPeople as $missingPerson) {
                $missingPersonImage = asset(public_path($missingPerson->image));
                $missingPeopleInformation [] = [
                    'id'            => $missingPerson->id ,
                    'name'          => $missingPerson->name ,
                    'gender'        => $missingPerson->gender , 
                    'description'   => $missingPerson->description ,
                    'country'       => $missingPerson->location->country ,
                    'state'         => $missingPerson->location->state ,
                    'city'          => $missingPerson->location->city ,
                    'losted_at'     => $missingPerson->losted_at ,
                    'image'         => $missingPersonImage ,
                ];
            }

            $foundedPeopleInformation = [];
            foreach ($user->foundedPeople as $foundedPerson) {
                $foundedPersonImage = asset(public_path($foundedPerson->image));
                $foundedPeopleInformation [] = [
                    'id'            => $foundedPerson->id ,
                    'name'          => $foundedPerson->name ,
                    'gender'        => $foundedPerson->gender , 
                    'description'   => $foundedPerson->description ,
                    'country'       => $foundedPerson->location->country ,
                    'state'         => $foundedPerson->location->state ,
                    'city'          => $foundedPerson->location->city ,
                    'police_station'=> $foundedPerson->policeStation->name ,
                    'founded_at'    => $foundedPerson->founded_at ,
                    'image'         => $foundedPersonImage ,
                ];
            }

            $allUsers [] = [
                'user' => $userInformation ,
                'missing_people' => $missingPeopleInformation ,
                'founded_people' => $foundedPeopleInformation ,
            ];
            
        }

        return $this->apiResponse( 200 , 'All Users with their missing and founded People' , null , $allUsers);
        
    }

    public function getUser() 
    {

        $userData = [];
        $user = User::where('id' , Auth::user()->id)
        ->with(['missingPeople' , 'foundedPeople'])
        ->first();

            $userProfileImage = asset(public_path($user->profile_image));
            $userInformation = [
                'name'          => $user->name ,
                'phone'         => $user->phone ,
                'gender'        => $user->gender ,
                'country'       => $user->country ,
                'state'         => $user->state ,
                'city'          => $user->city ,
                'profile_image' => $userProfileImage ,
            ];
            
            $missingPeopleInformation = [];
            foreach ($user->missingPeople as $missingPerson) {
                $missingPersonImage = asset(public_path($missingPerson->image));
                $missingPeopleInformation [] = [
                    'id'            => $missingPerson->id ,
                    'name'          => $missingPerson->name ,
                    'gender'        => $missingPerson->gender , 
                    'description'   => $missingPerson->description ,
                    'country'       => $missingPerson->location->country ,
                    'state'         => $missingPerson->location->state ,
                    'city'          => $missingPerson->location->city ,
                    'losted_at'     => $missingPerson->losted_at ,
                    'image'         => $missingPersonImage ,
                ];
            }

            $foundedPeopleInformation = [];
            foreach ($user->foundedPeople as $foundedPerson) {
                $foundedPersonImage = asset(public_path($foundedPerson->image));
                $foundedPeopleInformation [] = [
                    'id'            => $foundedPerson->id ,
                    'name'          => $foundedPerson->name ,
                    'gender'        => $foundedPerson->gender , 
                    'description'   => $foundedPerson->description ,
                    'country'       => $foundedPerson->location->country ,
                    'state'         => $foundedPerson->location->state ,
                    'city'          => $foundedPerson->location->city ,
                    'police_station'=> $foundedPerson->policeStation->name ,
                    'founded_at'    => $foundedPerson->founded_at ,
                    'image'         => $foundedPersonImage ,
                ];
            }

            $userData [] = [
                'user' => $userInformation ,
                'missing_people' => $missingPeopleInformation ,
                'founded_people' => $foundedPeopleInformation ,
            ];
            
        

        return $this->apiResponse( 200 , 'User with his missing and founded People' , null , $userData);
        
    }

    public function updateUserAccount($request)
    {
        $validation = Validator::make($request->all() ,[
            'name'                      => 'min:3 | max:50 ' ,
            'gender'                    => 'in:male,female',
            'national_id_front_image'   => 'image | mimes:jpeg,png,jpg | max:2048' ,
            'national_id_back_image'    => 'image | mimes:jpeg,png,jpg | max:2048' ,
            'profile_image'             => 'image | mimes:jpeg,png,jpg | max:2048' ,
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }

        if ($request->has('country') && $request->has('state') && $request->has('city'))
        {
            $location = Location::where([['country' , $request->country] , ['state' , $request->state] , ['city' , $request->city] ])->first();

            if ( is_null($location) )
            {
                $location = Location::create([
                    'country'   => $request->country ,
                    'state'     => $request->state ,
                    'city'      => $request->city ,
                ]);
            }
        }


        $user = User::where('id' , Auth::user()->id)->first();


        if ($request->has('national_id_front_image'))
        {
            $nationalIdFrontImageName = $this->setImageName($request->national_id_front_image , 'national_id_front_image');
            $this->uploadImage($request->national_id_front_image , $nationalIdFrontImageName , 'user\national_id_front_images' , $user->national_id_front_image);
        }
        if ($request->has('national_id_back_image'))
        {
            $nationalIdBackImageName = $this->setImageName($request->national_id_back_image , 'national_id_back_image');
            $this->uploadImage($request->national_id_back_image , $nationalIdBackImageName , 'user\national_id_back_images' , $user->national_id_back_image);
        }
        if ($request->has('profile_image'))
        {
            $profileImageName = $this->setImageName($request->profile_image , 'personal_image');
            $this->uploadImage($request->profile_image , $profileImageName , 'user\profile_images' , $user->profile_image);
        }

        $user->update([
            'name'                      => ( $request->has('name')                                                              ? $request->name            : $user->name ) ,
            'gender'                    => ( $request->has('gender')                                                            ? $request->gender          : $user->gender ) ,
            'national_id_front_image'   => ( $request->has('national_id_front_image')                                           ? $nationalIdFrontImageName : array_slice(explode('\\',$user->national_id_front_image) , -1)[0] ) ,
            'national_id_back_image'    => ( $request->has('national_id_back_image')                                            ? $nationalIdBackImageName  : array_slice(explode('\\',$user->national_id_back_image) , -1)[0] ) ,
            'profile_image'             => ( $request->has('profile_image')                                                     ? $profileImageName         : array_slice(explode('\\',$user->profile_image) , -1)[0] ) ,
            'location_id'               => ( ( $request->has('country') && $request->has('state') && $request->has('city') )    ? $location->id             : $user->location_id ) ,

        ]);

        return $this->apiResponse(200 , 'Account updated successfully');
    }

    public function deleteUserAccount()
    {
        $user = User::where('id' , Auth::user()->id)->with(['missingPeople' , 'foundedPeople'])->first();

        // delete user uploaded images
        $this->unlinkImage([$user->profile_image , $user->national_id_front_image , $user->national_id_back_image ]);

        //delete founded people uploaded images 
        foreach($user->foundedPeople as $foundedPerson)
        {
            $this->unlinkImage($foundedPerson->image);
        }

        //delete missing person uploaded images with missing people
        $user->missingPeople()->detach();
        foreach($user->missingPeople as $missingPerson)
        {
            $this->unlinkImage($missingPerson->image);
            $missingPerson->delete();
        }

        $user->delete();

        return $this->apiResponse(200 , 'Account successfully deleted');

    }

}
?>