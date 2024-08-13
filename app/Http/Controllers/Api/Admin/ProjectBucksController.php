<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectBucksRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\ProjectResource;
use App\Models\ProjectBucks;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProjectBucksController extends Controller
{
    public function __construct(
        protected ProjectBucksRepositoryInterface $projectBucksRepository,
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuid($projectUuid);
        $this->authorize('viewbucks', $project);

        $bucks = $this->projectBucksRepository->index($project);

        $project = new ProjectResource($project);

        return response()->json([
            'project' => $project,
            'bucks' => $bucks,
        ]);
    }
}
