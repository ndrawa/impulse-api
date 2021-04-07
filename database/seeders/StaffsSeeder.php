<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $faker = Faker::create();
        $uuidAdmin = Str::orderedUuid();
        DB::table('users')->insert([
            'id' => $uuidAdmin,
            'username' => 'admin',
            'password' => Hash::make('admin')
        ]);

        DB::table('staffs')->insert([
            'id' => 9999,
            'nip' => 1,
            'name' => 'Admin',
            'user_id' => $uuidAdmin,
            'code' => '-',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $initial_nip = 108500000;
        $data_staff_id = [];
        $data_name =    ['Achmad Benawi', 'Achmad Effendi', 'Aulia Arif Wardana', 'TJOKORDA AGUNG BUDI WIRAYUDA', 'DEDE ROHIDIN',
                        'GIA SEPTIANA WULANDARI', 'NUNGKI SELVIANDRO', 'ANDITYA ARIFIANTO', 'NIKEN DWI WAHYU CAHYANI', 'PRASTI EKO YUNANTO'];
        $data_staff_code =  ['BMW','AEF', 'UIW', 'COK', 'DDR',
                            'GIA', 'NKS', 'ADF', 'NKN', 'PEY'];
        $data_nip = [];



        for($i=0;$i<=9;$i++) {
            $nip = strval($initial_nip++);
            array_push($data_nip, $nip);
        }

        for($n=0;$n<=9;$n++) {
            $uuid = Str::orderedUuid();
            DB::table('users')->insert([
                'id' => $uuid,
                'username' => $data_staff_code[$n],
                'password' => Hash::make('password')
            ]);

            DB::table('staffs')->insert([
                'nip' => $data_nip[$n],
                'name' => $data_name[$n],
                'user_id' => $uuid,
                'code' => $data_staff_code[$n],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
