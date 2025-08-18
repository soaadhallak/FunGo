<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPermission=[
            'manage places',
            'manage sales',
            'send notifications',
            'view story',
            'view reviews',
        ];
        $userPermission=[
            'view places',
            'add to favourites',
            'create trip',
            'manage story',
            'rate place',
            'view place sales',
        ];
        $permissions=array_merge($adminPermission,$userPermission);

        foreach($permissions as $permission){
            Permission::firstOrCreate(['name'=>$permission]);
        }
        
        $user=Role::firstOrCreate(['name'=>'user']);
        $admin=Role::firstOrCreate(['name'=>'admin']);
        $user->syncPermissions($userPermission);
        $admin->syncPermissions($adminPermission);
    }
}
