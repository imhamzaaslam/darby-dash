<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $roles = [
                'admin',
                'pm',
                'developer',
                'editor',
                'viewer'
            ];
    
            foreach ($roles as $role) {
                if (!Role::where('name', $role)->exists()) {
                    Role::create(['name' => $role]);
                }
            }

            $permissions = [
                'add',
                'view',
                'update',
                'delete',
            ];

            foreach ($permissions as $permission) {
                if (!Permission::where('name', $permission)->exists()) {
                    Permission::create(['name' => $permission]);
                }
            }

            $role = Role::findByName('admin');
            $role->givePermissionTo(Permission::all());

            $role = Role::findByName('pm');
            $role->givePermissionTo(['add', 'view', 'update']);

            $role = Role::findByName('developer');
            $role->givePermissionTo(['add', 'view']);

            $role = Role::findByName('editor');
            $role->givePermissionTo(['view']);

            $role = Role::findByName('viewer');
            $role->givePermissionTo(['view']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}