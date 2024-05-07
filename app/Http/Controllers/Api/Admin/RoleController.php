<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\RoleRepositoryInterface;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository
    ) {}

    public function index(Request $request)
    {
        try {
            $roles = $this->roleRepository->all();

            return response()->json([
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while getting the roles. Please try again later.'
            ], 500);
        }
    }
}
