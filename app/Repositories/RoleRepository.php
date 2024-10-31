<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\RoleRepositoryInterface;
use App\Models\Base;
use App\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository extends AbstractEloquentRepository implements RoleRepositoryInterface
{
    /**
     * @var Role
     */
    protected Base $model;

    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function get(): Collection
    {
        return $this->model->where('name', '!=', 'super admin')->get();
    }

    public function getPermissions(Role $role): array
    {
        $permissions = $role->permissions;
        $permissionCategories = [
            'User Management' => ['create', 'view', 'edit', 'delete'],
            'Project Management' => ['create', 'view', 'edit', 'delete'],
            'Task Management' => ['create', 'view', 'edit', 'delete'],
            'Milestone Management' => ['create', 'view', 'edit', 'delete'],
            'Calendar Management' => ['create', 'view', 'edit', 'delete'],
            'File Management' => ['create', 'view', 'edit', 'delete'],
            'Team Management' => ['create', 'view', 'edit', 'delete'],
            'Payment Management' => ['create', 'view', 'edit', 'delete'],
        ];

        $formattedPermissions = [];
        foreach ($permissionCategories as $category => $actions) {
            $permissionData = [
                'name' => $category,
                'create' => false,
                'view' => false,
                'edit' => false,
                'delete' => false,
            ];

            $categoryWords = explode(' ', $category);
            $category = $categoryWords[0];

            foreach ($actions as $action) {
                $permissionName = strtolower($category . '-' . $action);
                if ($permissions->contains('name', $permissionName)) {
                    $permissionData[$action] = true;
                }
            }

            $formattedPermissions[] = $permissionData;
        }

        return $formattedPermissions;
    }

    public function createPermissions(Role $role, array $permissions): void
    {
        foreach ($permissions as $permission) {
            $module = $permission['name'];
            $categoryWords = explode(' ', $module);
            $category = $categoryWords[0];

            foreach (['create', 'view', 'edit', 'delete'] as $action) {
                $permissionName = strtolower($category . '-' . $action);
                if ($permission[$action]) {
                    $role->givePermissionTo($permissionName);
                } else {
                    $role->revokePermissionTo($permissionName);
                }
            }
        }
    }

//     "permissions" => array:8 [
//     0 => array:5 [
//       "name" => "User Management"
//       "create" => false
//       "view" => false
//       "edit" => false
//       "delete" => false
//     ]
//     1 => array:5 [
//       "name" => "Project Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//     2 => array:5 [
//       "name" => "Task Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//     3 => array:5 [
//       "name" => "Milestone Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//     4 => array:5 [
//       "name" => "Calendar Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//     5 => array:5 [
//       "name" => "File Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//     6 => array:5 [
//       "name" => "Team Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//     7 => array:5 [
//       "name" => "Payment Management"
//       "create" => true
//       "view" => true
//       "edit" => true
//       "delete" => true
//     ]
//   ]


// <?php

// namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

// class RolesAndPermissionsSeeder extends Seeder
// {
//     public function run()
//     {
//         // Reset cached roles and permissions
//         app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

//         $this->createPermissions();
        
//     }

//     private function permissions(): array
//     {
//         return [
//             'user' => ['create', 'view', 'edit', 'delete'],
//             'project' => ['create', 'view', 'edit', 'delete'],
//             'task' => ['create', 'view', 'edit', 'delete'],
//             'milestone' => ['create', 'view', 'edit', 'delete'],
//             'calendar' => ['create', 'view', 'edit', 'delete'],
//             'file' => ['create', 'view', 'edit', 'delete'],
//             'team' => ['create', 'view', 'edit', 'delete'],
//             'payment' => ['create', 'view', 'edit', 'delete'],
//         ];
//     }

//     private function roles(): array
//     {
//         return [
//             'Super Admin' => [
//                 'user' => ['create', 'view', 'edit', 'delete'],
//                 'project' => ['create', 'view', 'edit', 'delete'],
//                 'task' => ['create', 'view', 'edit', 'delete'],
//                 'milestone' => ['create', 'view', 'edit', 'delete'],
//                 'calendar' => ['create', 'view', 'edit', 'delete'],
//                 'file' => ['create', 'view', 'edit', 'delete'],
//                 'team' => ['create', 'view', 'edit', 'delete'],
//                 'payment' => ['create', 'view', 'edit', 'delete'],
//             ],
//             'Project Manager' => [
//                 'project' => ['create', 'view', 'edit', 'delete'],
//                 'task' => ['create', 'view', 'edit', 'delete'],
//                 'milestone' => ['create', 'view', 'edit', 'delete'],
//                 'calendar' => ['create', 'view', 'edit', 'delete'],
//                 'file' => ['create', 'view', 'edit', 'delete'],
//                 'team' => ['create', 'view', 'edit', 'delete'],
//                 'payment' => ['create', 'view', 'edit', 'delete'],
//             ],
//             'Client User' => [
//                 'project' => ['view'],
//                 'task' => ['view'],
//                 'milestone' => ['view'],
//                 'calendar' => ['view'],
//                 'file' => ['view'],
//                 'team' => ['view'],
//                 'payment' => ['view'],
//             ],
//             'Staff User' => [
//                 'project' => ['view'],
//                 'task' => ['view'],
//                 'milestone' => ['view'],
//                 'calendar' => ['view'],
//                 'file' => ['view'],
//                 'team' => ['view'],
//                 'payment' => ['view'],
//             ]
//         ];
//     }

//     private function createPermissions()
//     {
//         foreach ($this->permissions() as $module => $actions) {
//             foreach ($actions as $action) {
//                 Permission::create(['name' => "$module-$action"]);
//             }
//         }
//         $this->createRoles();
//     }

//     private function createRoles()
//     {
//         foreach ($this->roles() as $roleName => $modules) {
//             $role = Role::create(['name' => $roleName]);

//             foreach ($modules as $module => $actions) {
//                 foreach ($actions as $action) {
//                     $role->givePermissionTo("$module-$action");
//                 }
//             }
//         }
//     }
// }
}
