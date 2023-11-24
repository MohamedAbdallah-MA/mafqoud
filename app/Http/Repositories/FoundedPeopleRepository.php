<?php
namespace App\Http\Repositories;

use App\Models\Location;
use App\Models\FoundedPerson;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Http;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Interfaces\FoundedPeopleInterface;

class FoundedPeopleRepository implements FoundedPeopleInterface 
{
    use ApiResponseTrait;
    use ImageTrait;
    public function addFoundedPerson ($request)
    {
        $validation = Validator::make($request->all() , [
            'name'          => 'required | min:5 | max:50' ,
            'gender'        => 'required' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'founded_at'     => 'required' ,
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
        $this->uploadImage($request->image , $imageName , 'founded_people' );

        FoundedPerson::create([
            'name'          => $request->name ,
            'gender'        => $request->gender ,
            'description'   => $request->description ,
            'location_id'   => $location->id ,
            'image'         => $imageName       
        ]);

        return $this->apiResponse(200 , 'founded person information added successfully' );
    }

    public function getFoundedPeople ()
    {
        $founedPeople = FoundedPerson::with(['location' , 'searcher'])->get();

        $allFoundedPeople = [];
        foreach($founedPeople as $foundedPerson)
        {
            $foundedPersonImage = asset(public_path($foundedPerson->image));
            $foundedPeopleInformation = [
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


                $seacherProfileImage = asset(public_path($foundedPerson->searcher->profile_image));
                $searcherInformation = [
                    'name'          => $foundedPerson->searcher->name ,
                    'phone'         => $foundedPerson->searcher->phone ,
                    'profile_image' => $seacherProfileImage ,
                ];


            $allfoundedPeople[] = [
                'founded_person'    => $foundedPeopleInformation ,
                'searcher'         => $searcherInformation , 
            ];
        }

        return $this->apiResponse(200 , 'all founded people with their searchers' , null , $allfoundedPeople);
    }

    public function updateFoundedPersonInformation ($request)
    {
        $validation = Validator::make($request->all() , [
            'id'            => 'required | exists:founded_people,id' ,
            'name'          => 'required | min:5 | max:50' ,
            'gender'        => 'required' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'founded_at'    => 'required' ,
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

        $foundedPerson = FoundedPerson::find($request->id);

        //* set image's names
        $imageName = $this->setImageName($request->image , 'image');

        //* upload images to server   
        $this->uploadImage($request->image , $imageName , 'founded_people' , $foundedPerson->image);

        $foundedPerson->update([
            'name'          => $request->name ,
            'gender'        => $request->gender ,
            'description'   => $request->description ,
            'location_id'   => $location->id ,
            'image'         => $imageName       
        ]);

        return $this->apiResponse(200 , 'founded person information updated successfully' );
    }

    public function deleteFoundedPersonInformation ($foundedPersonId)
    {
        $foundedPerson = FoundedPerson::find($foundedPersonId);
        $foundedPerson->delete();
        return $this->apiResponse(200 , 'founded person information deleted successfully');
    }
}

?>