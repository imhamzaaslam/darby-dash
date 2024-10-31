<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\UserRole;
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
            'user' => ['create', 'view', 'edit', 'delete'],
            'project' => ['create', 'view', 'edit', 'delete'],
            'task' => ['create', 'view', 'edit', 'delete'],
            'milestone' => ['create', 'view', 'edit', 'delete'],
            'calendar' => ['create', 'view', 'edit', 'delete'],
            'file' => ['create', 'view', 'edit', 'delete'],
            'team' => ['create', 'view', 'edit', 'delete'],
            'payment' => ['create', 'view', 'edit', 'delete'],
        ];
    }

    private function roles(): array
    {
        return [
            UserRole::ADMIN->value => [
                'user' => ['create', 'view', 'edit', 'delete'],
                'project' => ['create', 'view', 'edit', 'delete'],
                'task' => ['create', 'view', 'edit', 'delete'],
                'milestone' => ['create', 'view', 'edit', 'delete'],
                'calendar' => ['create', 'view', 'edit', 'delete'],
                'file' => ['create', 'view', 'edit', 'delete'],
                'team' => ['create', 'view', 'edit', 'delete'],
                'payment' => ['create', 'view', 'edit', 'delete'],
            ],
            UserRole::PROJECT_MANAGER->value => [
                'project' => ['create', 'view', 'edit', 'delete'],
                'task' => ['create', 'view', 'edit', 'delete'],
                'milestone' => ['create', 'view', 'edit', 'delete'],
                'calendar' => ['create', 'view', 'edit', 'delete'],
                'file' => ['create', 'view', 'edit', 'delete'],
                'team' => ['create', 'view', 'edit', 'delete'],
                'payment' => ['create', 'view', 'edit', 'delete'],
            ],
            UserRole::CLIENT->value => [
                'project' => ['view'],
                'task' => ['view'],
                'milestone' => ['view'],
                'calendar' => ['view'],
                'file' => ['view'],
                'team' => ['view'],
                'payment' => ['view'],
            ],
            UserRole::STAFF->value => [
                'project' => ['view'],
                'task' => ['view'],
                'milestone' => ['view'],
                'calendar' => ['view'],
                'file' => ['view'],
                'team' => ['view'],
                'payment' => ['view'],
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