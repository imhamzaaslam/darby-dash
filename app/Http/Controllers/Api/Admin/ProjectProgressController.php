<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectRepositoryInterface;
use App\Services\ProjectProgressService;
use Illuminate\Http\Request;

class ProjectProgressController extends Controller
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    public function index($uuid)
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $progress = (new ProjectProgressService())->getProgress($project);

        return response()->json(['data' => $progress]);
    }
}
