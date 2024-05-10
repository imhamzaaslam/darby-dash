<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\TaskRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\task\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository,
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param string $projecId
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(string $projecId, Request $request): AnonymousResourceCollection|JsonResponse
    {
        $tasks = $this->taskRepository->get($projecId);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTaskRequest  $request
     * @param string $projectId
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request, string $projectId): JsonResponse
    {
        $validated = $request->validated();
        $project = $this->projectRepository->getFirstBy('id', $projectId);

        $task = $this->taskRepository->create($project, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $projecId
     * @param string $id
     * @return TaskResource
     */
    public function show(string $projecId, string $id): TaskResource
    {
        $task = $this->taskRepository->getByUuidOrFail($id);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreTaskRequest $request
     * @param string $projectId
     * @param string $taskUuid
     * @return Response|JsonResponse
     */
    public function update(StoreTaskRequest $request, string $projectId, string $taskUuid): Response|JsonResponse
    {
        $validated = $request->validated();

        $project = $this->projectRepository->getFirstBy('id', $projectId);
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);

        $this->taskRepository->update($task, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $projectId
     * @param string $taskUuid
     * @return JsonResponse
     */
    public function delete(string $projectId, string $taskUuid): JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);

        $this->taskRepository->delete($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
