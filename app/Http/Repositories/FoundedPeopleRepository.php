<?php
namespace App\Http\Repositories;

use App\Models\Location;
use App\Models\FoundedPerson;
use App\Http\Traits\ImageTrait;
use Illuminate\Support\Facades\Auth;
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
            'name'          => 'required | min:3 | max:50' ,
            'gender'        => 'required | in:male,female' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'founded_at'    => 'required | date | before_or_equal:today' ,
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
        $this->uploadImage($request->image , $imageName , 'founded_people' );

        FoundedPerson::create([
            'name'          => $request->name ,
            'gender'        => $request->gender ,
            'description'   => $request->description ,
            'location_id'   => $location->id ,
            'founder_id'    => Auth::user()->id ,
            'image'         => $imageName       
        ]);

        return $this->apiResponse(200 , 'founded person information added successfully' );
    }

    public function getFoundedPeople ()
    {
        $foundedPeople = FoundedPerson::with(['location' , 'founder'])->get();

        $allFoundedPeople = [];
        if (! is_null($foundedPeople))
        {
            foreach($foundedPeople as $foundedPerson)
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
    
    
                $founderProfileImage = asset(public_path($foundedPerson->founder->profile_image));
                $founderInformation = [
                    'name'          => $foundedPerson->founder->name ,
                    'phone'         => $foundedPerson->founder->phone ,
                    'gender'        => $foundedPerson->gender ,
                    'country'       => $foundedPerson->country ,
                    'state'         => $foundedPerson->state ,
                    'city'          => $foundedPerson->city ,
                    'profile_image' => $founderProfileImage ,
                ];
    
    
                $allFoundedPeople[] = [
                    'founded_person'    => $foundedPeopleInformation ,
                    'founder'           => $founderInformation , 
                ];
            }
        }

        return $this->apiResponse(200 , 'all founded people with their founders' , null , $allFoundedPeople);
    }

    public function updateFoundedPersonData ($request)
    {
        $validation = Validator::make($request->all() , [
            'id'            => 'required | exists:founded_people,id' ,
            'name'          => 'min:3 | max:50' ,
            'gender'        => 'in:male,female' ,
            'description'   => 'max:200' ,
            'founded_at'    => 'date | before_or_equal:today' ,
            'image'         => 'image | mimes:jpeg,png,jpg | max:2048'
        ]);

        if($validation->fails())
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

        $foundedPerson = FoundedPerson::find($request->id);
        if($request->has('image'))
        {
            //* set image's names
            $imageName = $this->setImageName($request->image , 'image');
            
            //* upload images to server   
            $this->uploadImage($request->image , $imageName , 'founded_people' , $foundedPerson->image);
        }
        
        
        $foundedPerson->update([
            'name'          => ( $request->has('name')                                                              ? $request->name        : $foundedPerson->name ) ,
            'gender'        => ( $request->has('gender')                                                            ? $request->gender      : $foundedPerson->gender ) ,
            'description'   => ( $request->has('description')                                                       ? $request->description : $foundedPerson->description ) ,
            'founded_at'    => ( $request->has('founded->at')                                                       ? $request->founded_at  : $foundedPerson->founded_at) ,
            'location_id'   => ( ( $request->has('country') && $request->has('state') && $request->has('city') )    ? $location->id         : $foundedPerson->location_id ) ,
            'image'         => ( $request->has('image')                                                             ? $imageName            : array_slice( explode('\\' , $foundedPerson->image) , -1  )[0] ) , 
        ]);

        return $this->apiResponse(200 , 'founded person information updated successfully' );
    }

    public function deleteFoundedPersonData ($foundedPersonId)
    {
        $validation = Validator::make(['id' => $foundedPersonId], [
            'id' => 'required|exists:founded_people,id',
        ]);

        if ($validation->fails())
        {
            return $this->apiResponse(422 , 'validation error' , $validation->errors());
        }

        $foundedPerson = FoundedPerson::find($foundedPersonId);

        //* remove image from server
        $this->unlinkImage($foundedPerson->image);

        $foundedPerson->delete();

        return $this->apiResponse(200 , 'founded person information deleted successfully');
    }
}

?>