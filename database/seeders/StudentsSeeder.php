<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use Carbon\Carbon;


class StudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $faker = Faker::create();

        $initial_nim = 1301184299;
        $gender_valid = ['male','female'];
        $religion_valid = ['islam','katolik','protestan','hindu','budha','kong hu cu'];
        $data_stud_id = [];
        $data_name =    ['Dani Andhika P', 'Indra Wahyudi', 'Fariz Muhammad Rizky', 'Putri Ester S', 'Hafshah Windjatika',
                        'Edgar Vigo', 'Muhammad Figo Akbar', 'Hafidz Lazuardi', 'Saskia Putri A', 'Muhammad Raihan M',
                        'Adabi Raihan', 'Ridha Novia', 'Hilmy Shabir Rusmarsidik', 'Mohammad Dwiantara Mahardhika', 'Annisya Hayati S',
                        'Aldy Renaldi', 'Sya Raihan Heggi', 'Arva W', 'Hussain', 'Anis Novitasari'];
        $data_nim = [];
        $data_gender =  ['male', 'male','male', 'female', 'female',
                        'male', 'male', 'male', 'female', 'male',
                        'male', 'female', 'male', 'male', 'female',
                        'male', 'male', 'male', 'male', 'female'];
        $data_religion =    ['protestan', 'katolik', 'islam', 'protestan', 'islam',
                            'islam', 'islam', 'islam', 'islam', 'hindu',
                            'islam', 'islam', 'islam', 'islam', 'protestan',
                            'budha', 'islam', 'islam', 'islam', 'islam'];


        for($i=0;$i<=50;$i++) {
            $nim = strval($initial_nim++);
            array_push($data_nim, $nim);
        }

        for($n=0;$n<=19;$n++) {
            $uuid = Str::orderedUuid();
            DB::table('users')->insert([
                'id' => $uuid,
                'username' => $data_nim[$n],
                'password' => Hash::make('password')
            ]);

            DB::table('students')->insert([
                'nim' => $data_nim[$n],
                'name' => $data_name[$n],
                'user_id' => $uuid,
                'gender' => $data_gender[$n],
                'religion' => $data_religion[$n],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
