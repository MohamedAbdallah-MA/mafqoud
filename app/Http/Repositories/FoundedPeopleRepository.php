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
use App\Models\PoliceStation;

class FoundedPeopleRepository implements FoundedPeopleInterface 
{
    use ApiResponseTrait;
    use ImageTrait;
    public function addFoundedPerson ($request)
    {
        $validation = Validator::make($request->all() , [
            'name'          => 'min:3 | max:50' ,
            'gender'        => 'required | in:male,female' ,
            'description'   => 'max:200' ,
            'country'       => 'required' ,
            'state'         => 'required' ,
            'city'          => 'required' ,
            'police_station'=> 'required | exists:police_stations,name',
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
        

        // create a new location if the location not exist 
        $location = Location::where([['country' , $request->country] , ['state' , $request->state] , ['city' , $request->city] ])->first();
        
        if ( is_null($location) )
        {
            $location = Location::create([
                'country'   => $request->country ,
                'state'     => $request->state ,
                'city'      => $request->city ,
            ]);
        }

        // make sure that police station choosen have the right location
        $policeStation = PoliceStation::where('name' , $request->police_station)
            ->with('location')
            ->whereHas('location' , function ($query) use ($request) {
                $query->where([['country' , $request->country],['state' , $request->state]]);
            })
            ->first();
            
        if ( is_null($policeStation))
        {
            return $this->apiResponse( 400 , 'police station not in that location' );
        }
        

        // set image's names
        $imageName = $this->setImageName($request->image , 'image');

        // upload images to server   
        $this->uploadImage($request->image , $imageName , 'founded_people' );

        FoundedPerson::create([
            'name'              => ($request->has('name')       ? $request->name        : null ) ,
            'gender'            => $request->gender ,
            'description'       => ($request->has('description')? $request->description : null ) ,
            'location_id'       => $location->id ,
            'police_station_id' => $policeStation->id ,
            'founder_id'        => Auth::user()->id ,
            'founded_at'        => $request->founded_at ,
            'image'             => $imageName       
        ]);

        return $this->apiResponse(200 , 'founded person information added successfully' );
    }

    public function getFoundedPeople ()
    {
        $foundedPeople = FoundedPerson::with(['location' , 'founder' , 'policeStation'])->get();

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
                    'police_station'=> $foundedPerson->policeStation->name ,
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
            'id'                => 'required | exists:founded_people,id' ,
            'name'              => 'min:3 | max:50' ,
            'gender'            => 'in:male,female' ,
            'description'       => 'max:200' ,
            'police_station'    => 'exists:police_stations,name' ,
            'founded_at'        => 'date | before_or_equal:today' ,
            'image'             => 'image | mimes:jpeg,png,jpg | max:2048'
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
        
                
        $foundedPerson = FoundedPerson::with(['policeStation','location'])
            ->find($request->id);

        if ($request->has('police_station'))
        {
            if ($request->has('country') && $request->has('state') && $request->has('city'))
            {
                $policeStation = PoliceStation::where('name',$request->police_station)
                    ->with('location')
                    ->whereHas('location' , function ($query) use ($request){
                        $query->where([['country',$request->country],['state',$request->state]]);
                    })
                    ->first();

                    if ( is_null($policeStation))
                    {
                        return $this->apiResponse( 400 , 'police station not in that location' );
                    }
            }
            else
            {
                $policeStation = PoliceStation::where('name',$request->police_station)
                    ->with('location')
                    ->whereHas('location' , function ($query) use ($foundedPerson){
                        $query->where([['country',$foundedPerson->location->country],['state',$foundedPerson->location->state]]);
                    })
                    ->first();

                if (is_null($policeStation))
                {
                    return $this->apiResponse( 400 , 'police station not in that location' );
                }
            }
                
            }
            else
            {
                if($request->has('country') && $request->has('state') && $request->has('city'))
                {
                    $policeStation = PoliceStation::where('name',$foundedPerson->policeStation->name)
                        ->with('location')
                        ->whereHas('location' , function ($query) use ($request){
                            $query->where([['country',$request->country],['state',$request->state]]);
                        })
                        ->first();
                    
                    if ( is_null($policeStation))
                    {
                        return $this->apiResponse( 400 , 'change the police station','location you provided doesn\'t have the police station' );
                    }
                }
        }


        if($request->has('image'))
        {
            // set image's names
            $imageName = $this->setImageName($request->image , 'image');
            
            // upload images to server   
            $this->uploadImage($request->image , $imageName , 'founded_people' , $foundedPerson->image);
        }
        
        
        $foundedPerson->update([
            'name'              => ( $request->has('name')                                                              ? $request->name        : $foundedPerson->name ) ,
            'gender'            => ( $request->has('gender')                                                            ? $request->gender      : $foundedPerson->gender ) ,
            'description'       => ( $request->has('description')                                                       ? $request->description : $foundedPerson->description ) ,
            'founded_at'        => ( $request->has('founded->at')                                                       ? $request->founded_at  : $foundedPerson->founded_at) ,
            'police_station_id' => ( $request->has('police_station')                                                    ? $policeStation->id    : $foundedPerson->police_station_id),
            'location_id'       => ( ( $request->has('country') && $request->has('state') && $request->has('city') )    ? $location->id         : $foundedPerson->location_id ) ,
            'image'             => ( $request->has('image')                                                             ? $imageName            : array_slice( explode('\\' , $foundedPerson->image) , -1  )[0] ) , 
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