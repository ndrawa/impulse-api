<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'add users']);

        $role1 = Role::create(['name' => 'laboran']);
        $role1->givePermissionTo('add users');

        $role2 = Role::create(['name' => 'students']);
        $role3 = Role::create(['name' => 'staffs']);

        $userAdmin = User::where('username','=','admin')->first();
        $userAdmin->assignRole($role1);
    }
}
