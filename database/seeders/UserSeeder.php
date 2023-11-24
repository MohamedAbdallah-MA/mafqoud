<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'  =>  'mohamed' ,
                'phone' =>  '+201125827873' ,
                'password'  =>  Hash::make('Mohamed2!') ,
                'national_id_front_image'   =>  '1_national_id_front_image.png' ,
                'national_id_back_image'   =>  '1_national_id_back_image.png' ,
                'profile_image'   =>  '1_profile_image.png' ,

            ],
        ];

        foreach ($users as $user)
        {
            User::create([
                'name'  =>  $user['name'] ,
                'phone' =>  $user['phone'] ,
                'password'  =>  $user['password'] ,
                'national_id_front_image'   =>  $user['national_id_front_image'] ,
                'national_id_back_image'   =>  $user['national_id_back_image'] ,
                'profile_image'   =>  $user['profile_image'] ,
            ]);
        }
    }
}
