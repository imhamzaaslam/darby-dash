<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\RoleRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository
    ) {}

    public function index(Request $request)
    {
        $roles = $this->roleRepository->get();
        return RoleResource::collection($roles);
    }
}
