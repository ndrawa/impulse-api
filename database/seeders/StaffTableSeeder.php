<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Role;

class StaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Staff::create([
            'nip' => 'laboran',
            'name' => 'Laboran (Super admin)',
            'code' => 'laboran'
        ]);
        $admin->save();
        // assign role
        $user = $admin->user;
        $user->assignRole(Role::ROLE_LABORAN);

        $staff = Staff::create([
            'nip' => '99120385',
            'name' => 'ABDURRAHMAN JOKO SUSILO',
            'code' => 'AJS'
        ]);
    }
}
