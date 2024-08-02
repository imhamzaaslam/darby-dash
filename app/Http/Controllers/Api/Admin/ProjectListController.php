<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectListRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\ProjectListResource;
use App\Http\Requests\ProjectList\StoreProjectListRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectListController extends Controller
{
    public function __construct(
        protected ProjectListRepositoryInterface $projectListRepository,
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
        $this->authorize('view', $project);
        $projectLists  = $this->projectListRepository->getProjectLists($project);
        
        return ProjectListResource::collection($projectLists);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function store(StoreProjectListRequest $request, string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('create', $project);
        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'project_id' => $project->id,
        ];

        $projectList = $this->projectListRepository->create($attributes);

        return (new ProjectListResource($projectList))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $uuid
     * @param string $listUuid
     * @return JsonResponse
     */
    public function update(StoreProjectListRequest $request, string $uuid, string $listUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('update', $project);
        $projectList = $this->projectListRepository->getByUuidOrFail($listUuid);

        $validated = $request->validated();

        $attributes = [
            'name' => $validated['name'],
            'project_id' => $project->id,
        ];

        $this->projectListRepository->update($projectList, $attributes);

        return (new ProjectListResource($projectList))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @param string $ListUuid
     * @return JsonResponse
     */
    public function delete(string $uuid, string $ListUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('delete', $project);
        $projectList = $this->projectListRepository->getByUuidOrFail($ListUuid);
        $this->projectListRepository->delete($projectList);

        return response()->json(['message' => 'Project List deleted successfully']);
    }

    /**
     * Get tasks by project.
     *
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getListWithoutMilestone(string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('view', $project);
        $projectLists = $this->projectListRepository->getListWithoutMilestone($project);

        return ProjectListResource::collection($projectLists);
    }

}
