<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectTypeRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectTypeResource;

class ProjectTypeController extends Controller
{
    public function __construct(
        protected ProjectTypeRepositoryInterface $projectTypeRepository
    ) {}

    public function index(Request $request)
    {
        $roles = $this->projectTypeRepository->getAll();
        return ProjectTypeResource::collection($roles);
    }
}
