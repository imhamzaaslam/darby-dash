<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\TaskRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\ProjectListRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\task\StoreTaskRequest;
use App\Http\Requests\task\UpdateTaskRequest;
use App\Http\Requests\task\StoreTaskByProjectRequest;
use App\Http\Requests\task\StoreProjectTasksOrderRequest;
use App\Http\Requests\file\StoreFileRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\FileResource;
use App\Services\FileResolverService;
use App\Services\FileUploadService;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository,
        protected ProjectRepositoryInterface $projectRepository,
        protected ProjectListRepositoryInterface $projectListRepository,
        protected FileRepositoryInterface $fileRepository,
        protected FileResolverService $fileResolverService,
        protected FileUploadService $fileUploadService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param string $listUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(string $listUuid): AnonymousResourceCollection|JsonResponse
    {
        $projectList = $this->projectListRepository->getByUuidOrFail($listUuid);
        $tasks = $this->projectListRepository->getListTasks($projectList);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTaskRequest  $request
     * @param string $listUuid
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request, string $listUuid): JsonResponse
    {
        $list = $this->projectListRepository->getByUuidOrFail($listUuid);
        $validated = $request->validated();

        $res = $this->taskRepository->create($list, $validated);
        $task = $this->taskRepository->getByUuidOrFail($res->uuid);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreTaskRequest $request
     * @param string $listUuid
     * @param string $taskUuid
     * @return Response|JsonResponse
     */
    public function update(StoreTaskRequest $request, string $listUuid, string $taskUuid): Response|JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);
        $validated = $request->validated();

        $this->taskRepository->update($task, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $listUuid
     * @param string $taskUuid
     * @return JsonResponse
     */
    public function delete(string $listUuid, string $taskUuid): JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);

        $this->taskRepository->delete($task);

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Get tasks by project.
     *
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function fetchUnlistedTasks(string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $tasks = $this->taskRepository->fetchUnlistedTasks($project);

        return TaskResource::collection($tasks);
    }

    /**
     * Get tasks by project.
     *
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getByProject(string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $tasks = $this->taskRepository->getByProject($project);

        return TaskResource::collection($tasks);
    }

    /**
     * Store task by project.
     *
     * @param StoreTaskByProjectRequest $request
     * @param string $projectUuid
     * @return JsonResponse
     */
    public function storeByProject(StoreTaskByProjectRequest $request, string $projectUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $validated = $request->validated();

        $task = $this->taskRepository->createByProject($project, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update task by project.
     *
     * @param StoreTaskByProjectRequest $request
     * @param string $projectUuid
     * @param string $taskUuid
     * @return Response|JsonResponse
     */
    public function updateByProject(StoreTaskByProjectRequest $request, string $projectUuid, string $taskUuid): Response|JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);
        $validated = $request->validated();

        $this->taskRepository->update($task, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get files by task.
     *
     * @param Request $request
     * @param string $taskUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getFiles(Request $request, string $taskUuid): AnonymousResourceCollection|JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);
        $fileResolver = $this->fileResolverService->resolve($request->segments(), $taskUuid);

        $files = $this->fileRepository->getAllByMorph($fileResolver['morph_type'], $fileResolver['morph_id']);

        return FileResource::collection($files);
    }

    /**
     * Store files to task.
     *
     * @param StoreFileRequest $request
     * @param string $taskUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function storeFiles(Request $request, string $taskUuid): AnonymousResourceCollection|JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);
        $files = $request->file('files');

        $fileResolver = $this->fileResolverService->resolve($request->segments(), $taskUuid);

        $disk = app()->environment('testing') ? 'testing' : 'public';

        $storedFiles = [];
        foreach ($files as $file) {
            $fileData = $this->fileUploadService->uploadFile(
                $file,
                $fileResolver['directory'],
                $disk,
                $file->getClientOriginalName()
            );

            $storedFiles[] = $this->fileRepository->store(
                $fileResolver['morph_type'],
                $fileResolver['morph_id'],
                $fileData
            );
        }

        return FileResource::collection($storedFiles);
    }

    /**
     * Update project tasks order.
     *
     * @param StoreProjectTasksOrderRequest $request
     * @param string $projectUuid
     * @param string $taskUuid
     * @return Response|JsonResponse
     */

    public function updateProjectTasksOrder(StoreProjectTasksOrderRequest $request, string $projectUuid, string $taskUuid): Response|JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);
        $validated = $request->validated();
        $this->taskRepository->updateTasksOrder($task, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Delete task by project.
     *
     * @param string $projectUuid
     * @param string $taskUuid
     * @return JsonResponse
     */
    public function deleteByProject(string $projectUuid, string $taskUuid): JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);

        $this->taskRepository->delete($task);

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Update task attributes.
     *
     * @param UpdateTaskRequest $request
     * @param string $taskUuid
     * @return Response|JsonResponse
     */
    public function updateAttributes(UpdateTaskRequest $request, string $taskUuid): Response|JsonResponse
    {
        $task = $this->taskRepository->getByUuidOrFail($taskUuid);
        $validated = $request->validated();

        $this->taskRepository->update($task, $validated);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(200);
    }
}
