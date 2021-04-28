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
        $laboran = Staff::create([
            'nip' => 'laboran',
            'name' => 'Laboran (Super admin)',
            'code' => 'laboran'
        ]);
        $laboran->save();
        // assign role
        $user = $laboran->user;
        $user->assignRole(Role::ROLE_LABORAN);

        $admin = Staff::create([
            'nip' => 'admin1',
            'name' => 'admin1',
            'code' => 'admin1'
        ]);
        $admin->save();
        // assign role
        $user = $admin->user;
        $user->assignRole(Role::ROLE_LABORAN);
        $user->assignRole(Role::ROLE_DOSEN);

        $staff = Staff::create([
            'nip' => '99120385',
            'name' => 'ABDURRAHMAN JOKO SUSILO',
            'code' => 'AJS'
        ]);
    }
}
