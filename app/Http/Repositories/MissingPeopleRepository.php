<?php
namespace App\Http\Repositories;

use App\Models\Location;
use App\Models\MissingPerson;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Http;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Interfaces\MissingPeopleInterface;

class MissingPeopleRepository implements MissingPeopleInterface 
{
    use ApiResponseTrait;
    use ImageTrait;
    public function addMissingPerson ($request)
    {
        $validation = Validator::make($request->all() , [
            'name'          => 'required | min:5 | max:50' ,
            'gender'        => 'required' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'losted_at'     => 'required' ,
            'image'         => 'required | image | mimes:jpeg,png,jpg | max:2048'
        ]);

        if($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }

        //! let it untill ai model Works
        // $request = Http::post('https://api.com' , [
        //     'image' => $image
        // ]);
        
        $location = Location::where([['country' , $request->country] , ['state' , $request->state] , ['city' , $request->city] ])->get();
        
        if ($location->isEmpty())
        {
            $location = Location::create([
                'country'   => $request->country ,
                'state'     => $request->state ,
                'city'      => $request->city ,
            ]);
        }else
        {
            $location->update([
                'country'   => $request->country ,
                'state'     => $request->state ,
                'city'      => $request->city ,
            ]);
        }


        //* set image's names
        $imageName = $this->setImageName($request->image , 'image');

        //* upload images to server   
        $this->uploadImage($request->image , $imageName , 'missing_people' );

        $missingPerson = MissingPerson::create([
            'name'          => $request->name ,
            'gender'        => $request->gender ,
            'description'   => $request->description ,
            'location_id'   => $location->id ,
            'image'         => $imageName       
        ]);

        return $this->apiResponse(200 , 'missing person information added successfully' );
    }

    public function getMissingPeople ()
    {
        $missingPeople = MissingPerson::with(['location' , 'searchers'])->get();

        $allMissingPeople = [];
        foreach($missingPeople as $missingPerson)
        {
            $missingPersonImage = asset(public_path($missingPerson->image));
            $missingPeopleInformation = [
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

            $searchersInformation = [];
            foreach($missingPerson->searchers as $searcher)
            {
                $seacherProfileImage = asset(public_path($searcher->profile_image));
                $searchersInformation[] = [
                    'name'          => $searcher->name ,
                    'phone'         => $searcher->phone ,
                    'profile_image' => $seacherProfileImage ,
                ];
            }

            $allMissingPeople[] = [
                'missing_person'    => $missingPeopleInformation ,
                'searchers'         => $searchersInformation , 
            ];
        }

        return $this->apiResponse(200 , 'all missing people with their searchers' , null , $allMissingPeople);
    }

    public function updateMissingPersonInformation ($request)
    {
        $validation = Validator::make($request->all() , [
            'id'            => 'required | exists:missing_people,id' ,
            'name'          => 'required | min:5 | max:50' ,
            'gender'        => 'required' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'losted_at'     => 'required' ,
            'image'         => 'required | image | mimes:jpeg,png,jpg | max:2048'
        ]);

        if($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }
        $location = Location::where([['country' , $request->country] , ['state' , $request->state] , ['city' , $request->city] ])->get();
        if ($location->isEmpty())
        {
            $location = Location::create([
                'country'   => $request->country ,
                'state'     => $request->state ,
                'city'      => $request->city ,
            ]);
        }else
        {
            $location->update([
                'country'   => $request->country ,
                'state'     => $request->state ,
                'city'      => $request->city ,
            ]);
        }

        $missingPerson = MissingPerson::find($request->id);

        //* set image's names
        $imageName = $this->setImageName($request->image , 'image');

        //* upload images to server   
        $this->uploadImage($request->image , $imageName , 'missing_people' , $missingPerson->image);

        $missingPerson->update([
            'name'          => $request->name ,
            'gender'        => $request->gender ,
            'description'   => $request->description ,
            'location_id'   => $location->id ,
            'image'         => $imageName       
        ]);

        return $this->apiResponse(200 , 'missing person information updated successfully' );
    }

    public function deleteMissingPersonInformation ($missingPersonId)
    {
        $missingPerson = MissingPerson::find($missingPersonId);
        $missingPerson->delete();
        return $this->apiResponse(200 , 'Missing person information deleted successfully');
    }
}

?>