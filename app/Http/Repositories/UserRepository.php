<?php
namespace App\Http\Repositories;
use App\Http\Traits\ImageTrait;
use App\Models\User;
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
                'name'  => $user->name ,
                'phone' => $user->phone ,
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
                'name'  => $user->name ,
                'phone' => $user->phone ,
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
            'name'                      =>  'required' ,
            'phone'                     =>  'required | unique:users,phone' ,
            'national_id_front_image'   =>  'required | image | mimes:jpeg,png,jpg | max:2048' ,
            'national_id_back_image'    =>  'required  | image | mimes:jpeg,png,jpg | max:2048' ,
            'profile_image'             =>  'required | image | mimes:jpeg,png,jpg | max:2048' ,
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }

        $user = User::where('id' , Auth::user()->id)->first();

        //* set image's names
        $nationalIdFrontImageName = $this->setImageName($request->national_id_front_image , 'national_id_front_image');
        $nationalIdBackImageName = $this->setImageName($request->national_id_back_image , 'national_id_back_image');
        $profileImageName = $this->setImageName($request->profile_image , 'personal_image');

        //* upload images to server   
        $this->uploadImage($request->national_id_front_image , $nationalIdFrontImageName , 'user\national_id_front_images' , $user->national_id_front_image);
        $this->uploadImage($request->national_id_back_image , $nationalIdBackImageName , 'user\national_id_back_images' , $user->national_id_back_image);
        $this->uploadImage($request->profile_image , $profileImageName , 'user\profile_images' , $user->profile_image);

        $user->update([
            'name'                      =>  $request->name ,
            'phone'                     =>  $request->phone ,
            'national_id_front_image'   =>  $nationalIdFrontImageName ,
            'national_id_back_image'    =>  $nationalIdBackImageName ,
            'profile_image'             =>  $profileImageName ,
        ]);

        return $this->apiResponse(200 , 'Account updated successfully');
    }

    public function deleteUserAccount()
    {
        $user = User::where('id' , Auth::user()->id)->with(['missingPeople' , 'foundedPeople'])->first();
        $user->delete();
        return $this->apiResponse(200 , 'Account successfully deleted');
    }

}
?>