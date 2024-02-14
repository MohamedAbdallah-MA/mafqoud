<?php
namespace App\Http\Repositories;

use App\Models\Location;
use App\Models\MissingPerson;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
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
            'name'          => 'required | min:3 | max:50' ,
            'gender'        => 'required | in:male,female' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'losted_at'     => 'required | date | before_or_equal:today' ,
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
        $imageName = $this->setImageName($request->image , 'image');

        //* upload images to server   
        $this->uploadImage($request->image , $imageName , 'missing_people' );

        $missingPerson = MissingPerson::create([
            'name'          => $request->name ,
            'gender'        => $request->gender ,
            'description'   => $request->description ,
            'location_id'   => $location->id ,
            'image'         => $imageName       
        ])->searchers()->attach(Auth::user()->id);

        return $this->apiResponse(200 , 'missing person information added successfully' );
    }

    public function getMissingPeople ()
    {
        $missingPeople = MissingPerson::with(['location' , 'searchers'])->get();

        $allMissingPeople = [];
        if ( ! is_null($missingPeople) )
        {
            foreach($missingPeople as $missingPerson)
            {
                $missingPersonImage = $this->getImageUrl($missingPerson->image);
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
                    $seacherProfileImage = $this->getImageUrl($searcher->profile_image);
                    $searchersInformation[] = [
                        'name'          => $searcher->name ,
                        'phone'         => $searcher->phone ,
                        'gender'        => $searcher->gender ,
                        'country'       => $searcher->country ,
                        'state'         => $searcher->state ,
                        'city'          => $searcher->city ,
                        'profile_image' => $seacherProfileImage ,
                    ];
                }
    
                $allMissingPeople[] = [
                    'missing_person'    => $missingPeopleInformation ,
                    'searchers'         => $searchersInformation , 
                ];
            }
        }

        return $this->apiResponse(200 , 'all missing people with their searchers' , null , $allMissingPeople);
    }

    public function updateMissingPersonData ($request)
    {
        $validation = Validator::make($request->all() , [
            'id'            => 'required | exists:missing_people,id' ,
            'name'          => 'min:3 | max:50' ,
            'gender'        => 'in:male,female' ,
            'description'   => 'max:200' ,
            'losted_at'     => 'date | before_or_equal:today' ,
            'image'         => 'image | mimes:jpeg,png,jpg | max:2048'
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }

        if ( $request->has('country') && $request->has('state') && $request->has('city') )
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

        $missingPerson = MissingPerson::find($request->id);

        if ( $request->has('image') )
        {
            //* set image's names
            $imageName = $this->setImageName($request->image , 'image');
    
            //* upload images to server   
            $this->uploadImage($request->image , $imageName , 'missing_people' , $missingPerson->image);
        }

        $missingPerson->update([
            'name'          => ( $request->has('name')                                                              ? $request->name        : $missingPerson->name ) ,
            'gender'        => ( $request->has('gender')                                                            ? $request->gender      : $missingPerson->gender ) ,
            'description'   => ( $request->has('description')                                                       ? $request->description : $missingPerson->description ) ,
            'losted_at'     => ( $request->has('losted->at')                                                        ? $request->losted_at   : $missingPerson->founded_at) ,
            'location_id'   => ( ( $request->has('country') && $request->has('state') && $request->has('city') )    ? $location->id         : $missingPerson->location_id ) ,
            'image'         => ( $request->has('image')                                                             ? $imageName            : array_slice( explode('/' , $missingPerson->image) , -1  )[0] ) , 
        ]);

        return $this->apiResponse(200 , 'missing person information updated successfully' );
    }

    public function deleteMissingPersonData ($missingPersonId)
    {
        $validation = Validator::make(['id' => $missingPersonId], [
            'id' => 'required|exists:missing_people,id',
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }

        $missingPerson = MissingPerson::find($missingPersonId);

        $missingPerson->searchers()->detach();

        $this->deleteImage($missingPerson->image);

        $missingPerson->delete();

        return $this->apiResponse(200 , 'Missing person information deleted successfully');
    }
}

?>