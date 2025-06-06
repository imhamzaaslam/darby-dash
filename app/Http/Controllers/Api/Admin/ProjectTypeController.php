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
        $projectTypes = $this->projectTypeRepository->getFirstBy('id', 1);
        return new ProjectTypeResource($projectTypes);
        //return ProjectTypeResource::collection($projectTypes);
    }
}
