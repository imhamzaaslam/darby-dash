<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\Project\StoreProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $projects = $this->projectRepository
            ->getAllRecordsQuery()
            ->filtered($request->keyword ?? '')
            ->ordered($request->orderBy ?? 'id', $request->order ?? 'asc')
            ->paginate($request->per_page ?? config('pagination.per_page', 10));
        

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectRequest  $request
     * @return JsonResponse
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $attributes = [
            'project_type_id' => $validated['project_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_manager_id' => $validated['project_manager_id'],
            'est_hours' => $validated['est_hours'],
            'est_budget' => $validated['est_budget'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
        ];

        $member_ids = explode(',', $validated['member_ids']);

        $project = $this->projectRepository->create($attributes);
        if (count($member_ids) > 0) {
            $this->projectRepository->storeProjectMembers($project, $member_ids);
        }

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return ProjectResource|JsonResponse
     */
    public function show(string $uuid): ProjectResource|JsonResponse
    {
        $project = $this->projectRepository->getByUuid($uuid);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreProjectRequest  $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(StoreProjectRequest $request, string $uuid): JsonResponse
    {
        $validated = $request->validated();

        $attributes = [
            'project_type_id' => $validated['project_type_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_manager_id' => $validated['project_manager_id'],
            'est_hours' => $validated['est_hours'],
            'est_budget' => $validated['est_budget'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $validated['status'],
        ];

        $member_ids = explode(',', $validated['member_ids']);

        $project = $this->projectRepository->update($uuid, $attributes);
        $this->projectRepository->updateProjectMembers($project, $member_ids);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function delete(string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);

        $this->projectRepository->delete($project);

        return response()->json(['message' => 'Project deleted successfully']);
    }
}