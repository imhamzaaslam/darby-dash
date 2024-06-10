<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\FolderRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\FolderResource;
use App\Http\Requests\project\StoreFolderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FolderController extends Controller
{
    public function __construct(
        protected FolderRepositoryInterface $folderRepository,
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
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $folders = $this->folderRepository->getBy('project_id', $project->id);

        return FolderResource::collection($folders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFolderRequest $request
     * @param string $projectUuid
     * @return FolderResource|JsonResponse
     */
    public function store(StoreFolderRequest $request, string $projectUuid): FolderResource|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $res = $this->folderRepository->create($project, $request->validated());

        $folder = $this->folderRepository->getByUuidOrFail($res->uuid);

        return new FolderResource($folder);
    }

    /**
     * Display the specified resource.
     *
     * @param StoreFolderRequest $request
     * @param string $folderUuid
     * @return FolderResource|JsonResponse
     */
    public function update(StoreFolderRequest $request, string $folderUuid): FolderResource|JsonResponse
    {
        $folder = $this->folderRepository->getByUuidOrFail($folderUuid);
        $this->folderRepository->update($folder, $request->validated());

        return new FolderResource($folder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $folderUuid
     * @return JsonResponse
     */
    public function delete(string $folderUuid): JsonResponse
    {
        $folder = $this->folderRepository->getByUuidOrFail($folderUuid);
        $this->folderRepository->delete($folder);

        return response()->json(['message' => 'Folder deleted successfully']);
    }
}
