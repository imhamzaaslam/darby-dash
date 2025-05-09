<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enums\UserRole;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
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
            ],
            UserRole::DEVELOPER->value => [
                'project' => ['view'],
                'task' => ['view'],
                'milestone' => ['view'],
                'calendar' => ['view'],
                'file' => ['view'],
                'team' => ['view'],
                'payment' => ['view'],
            ],
            UserRole::DESIGNER->value => [
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
                $permissionName = "$module-$action";
                if (!DB::table('permissions')->where('name', $permissionName)->exists()) {
                    DB::table('permissions')->insert([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
        $this->createRoles();
    }

    private function createRoles()
    {
        foreach ($this->roles() as $roleName => $modules) {
            if (!DB::table('roles')->where('name', $roleName)->exists()) {
                $roleId = DB::table('roles')->insertGetId([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
    
                foreach ($modules as $module => $actions) {
                    foreach ($actions as $action) {
                        $permissionName = "$module-$action";
                        $permission = DB::table('permissions')->where('name', $permissionName)->first();
                        if ($permission) {
                            if (!DB::table('role_has_permissions')
                                ->where('role_id', $roleId)
                                ->where('permission_id', $permission->id)
                                ->exists()) {
                                DB::table('role_has_permissions')->insert([
                                    'role_id' => $roleId,
                                    'permission_id' => $permission->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
