<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            Role::ROLE_LABORAN,
            Role::ROLE_STUDENT,
            Role::ROLE_ASPRAK,
            Role::ROLE_STAFF,
            Role::ROLE_ASLAB,
            Role::ROLE_DOSEN
        ];

        foreach($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
