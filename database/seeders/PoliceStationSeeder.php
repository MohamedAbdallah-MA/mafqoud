<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\PoliceStation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PoliceStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policeStations = [
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'maadi' ,
                'name'      => 'maadi' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'helwan' ,
                'name'      => 'helwan' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'giza' ,
                'name'      => 'giza' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => '15 may' ,
                'name'      => '15 may' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => '6 october 1' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => '6 october second' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'maadi' ,
                'name'      => 'basatin' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Khalifa' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'tabbin' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'The Red Trail' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'al rehab' ,
                'name'      => 'Al-Rehab' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Zawya Al-Hamra' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Zytoon' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'shobra' ,
                'name'      => 'Al-Sahel' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'al salam' ,
                'name'      => 'Al-Salam' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Sayyida Zeinab' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Sharabia' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'el shrouk' ,
                'name'      => 'Shrouk' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Zahir' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'New Cairo 1' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'New Cairo 2' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'New Cairo 3' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Marg' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Matareya' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Moski' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Nuzha' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Al-Waili' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Nasr City' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Bab Al-Shaareya' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Bulaq' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Hadayek Al-Qubba' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Helwan' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Rod Al-Farag' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Shubra' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Abdeen' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Kasr Elnile' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'badr' ,
                'name'      => 'Badr City' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Masr Algdeda' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Mase Alkadema' ,
            ],
            [
                'country'   => 'Egypt' ,
                'state'     => 'Cairo' ,
                'city'      => 'new cairo' ,
                'name'      => 'Manshiyet Nasser' ,
            ],

            
        ];

        foreach ($policeStations as $policeStation)
        {
            $location = Location::where([ ['country' , $policeStation['country']] , ['state' , $policeStation['state'] ] , ['city' , $policeStation['city'] ] ])->first();
            if(is_null($location))
            {
                $location = location::create([
                    'country'   => $policeStation['country'] ,
                    'state'     => $policeStation['state'] ,
                    'city'      => $policeStation['city'] ,
                ]);
            }
            PoliceStation::create([
                'name'          => $policeStation['name'],
                'location_id'   => $location->id ,
            ]);
        }
    }
}
