<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        
    }

    private function permissions(): array
    {
        return [
            'user' => ['add', 'view', 'update', 'delete'],
            'project' => ['add', 'view', 'update', 'delete'],
            'task' => ['add', 'view', 'update', 'delete'],
        ];
    }

    private function roles(): array
    {
        return [
            'Super Admin' => [
                'user' => ['add', 'view', 'update', 'delete'],
                'project' => ['add', 'view', 'update', 'delete'],
                'task' => ['add', 'view', 'update', 'delete'],
            ],
            'Project Manager' => [
                'project' => ['add', 'view', 'update', 'delete'],
                'task' => ['add', 'view', 'update', 'delete'],
            ],
            'Client User' => [
                'project' => ['view'],
                'task' => ['view', 'update'],
            ],
            'Staff User' => [
                'task' => ['view', 'update'],
                'project' => ['view'],
            ]
        ];
    }

    private function createPermissions()
    {
        foreach ($this->permissions() as $module => $actions) {
            foreach ($actions as $action) {
                Permission::create(['name' => "$module-$action"]);
            }
        }
        $this->createRoles();
    }

    private function createRoles()
    {
        foreach ($this->roles() as $roleName => $modules) {
            $role = Role::create(['name' => $roleName]);

            foreach ($modules as $module => $actions) {
                foreach ($actions as $action) {
                    $role->givePermissionTo("$module-$action");
                }
            }
        }
    }
}