<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\FileRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\FolderRepositoryInterface;
use App\Http\Resources\FileResource;
use App\Http\Requests\File\StoreFileRequest;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FileController extends Controller
{
    public function __construct(
        protected FileRepositoryInterface $fileRepository,
        protected ProjectRepositoryInterface $projectRepository,
        protected FolderRepositoryInterface $folderRepository,
        protected FileUploadService $fileUploadService,
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
        $files = $this->fileRepository->getProjectFiles($project);

        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFileRequest $request
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function store(StoreFileRequest $request, string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $files = $request->file('files');

        $validated = $request->validated();

        $folder = null;
        if (isset($validated['folder_uuid'])) {
            $folder = $this->folderRepository->getByUuidOrFail($validated['folder_uuid']);
        }

        $storedFiles = [];
        foreach ($files as $file) {
            $fileData = $this->fileUploadService->uploadFile($file, 'uploads', 'public');

            $attributes = [
                'name' => $fileData->name,
                'path' => $fileData->path,
                'url' => $fileData->url,
                'type' => $fileData->mimeType,
                'size' => $fileData->size,
                'project_id' => $project->id,
                'folder_id' => $folder ? $folder->id : null,
            ];

            $storedFiles[] = $this->fileRepository->create($attributes);
        }

        return FileResource::collection($storedFiles);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param string $fileUuid
     * @return JsonResponse
     */
    public function delete(Request $request, string $fileUuid): JsonResponse
    {
        $file = $this->fileRepository->getByUuidOrFail($fileUuid);

        $this->fileUploadService->deleteFile($file->path, 'public');

        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }

    /**
     * Get all files from a folder.
     *
     * @param string $folderUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getFiles(string $folderUuid): AnonymousResourceCollection|JsonResponse
    {
        $folder = $this->folderRepository->getByUuidOrFail($folderUuid);
        $files = $this->fileRepository->getBy('folder_id', $folder->id);

        return FileResource::collection($files);
    }

}
