<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectPhaseRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\ProjectPhaseResource;
use App\Http\Requests\ProjectPhase\StoreProjectPhaseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectPhaseController extends Controller
{
    public function __construct(
        protected ProjectPhaseRepositoryInterface $projectPhaseRepository,
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param string $uuid
     * @return AnonymousResourceCollection
     */
    public function index(string $uuid): AnonymousResourceCollection
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $projectPhases  = $this->projectPhaseRepository->getProjectPhases($project);
        
        return ProjectPhaseResource::collection($projectPhases);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function store(StoreProjectPhaseRequest $request, string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'project_id' => $project->id,
        ];

        $projectPhase = $this->projectPhaseRepository->create($attributes);

        return (new ProjectPhaseResource($projectPhase))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $uuid
     * @param string $phaseUuid
     * @return JsonResponse
     */
    public function update(StoreProjectPhaseRequest $request, string $uuid, string $phaseUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $projectPhase = $this->projectPhaseRepository->getByUuidOrFail($phaseUuid);

        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'project_id' => $project->id,
        ];

        $this->projectPhaseRepository->update($projectPhase, $attributes);

        return (new ProjectPhaseResource($projectPhase))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @param string $phaseUuid
     * @return JsonResponse
     */
    public function delete(string $uuid, string $phaseUuid): JsonResponse
    {
        $projectPhase = $this->projectPhaseRepository->getByUuidOrFail($phaseUuid);
        $this->projectPhaseRepository->delete($projectPhase);

        return response()->json(['message' => 'Project phase deleted successfully']);
    }
}
