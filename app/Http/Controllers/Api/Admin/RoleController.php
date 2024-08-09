<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\RoleRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use App\Http\Requests\role\UpdateRolePermissionsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $roles = $this->roleRepository->get();
        return RoleResource::collection($roles);
    }

    /**
     * Get permissions of a role
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPermissions($id): JsonResponse
    {
        $role = $this->roleRepository->getFirstByOrFail('id', $id);
        $formattedPermissions = $this->roleRepository->getPermissions($role);
        return response()->json($formattedPermissions);
    }

    /**
     * Update permissions of a role
     *
     * @param UpdateRolePermissionsRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updatePermissions(UpdateRolePermissionsRequest $request, $id)
    {
        $role = $this->roleRepository->getFirstByOrFail('id', $id);

        $data = $request->validated();
        $this->roleRepository->createPermissions($role, $data['permissions']);

        return response()->json(['message' => 'Permissions updated successfully']);
    }
}
