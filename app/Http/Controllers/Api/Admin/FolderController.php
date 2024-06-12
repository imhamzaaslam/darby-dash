<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\FolderRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\FolderResource;
use App\Http\Requests\project\StoreFolderRequest;
use App\Http\Requests\project\UpdateFolderRequest;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Services\FileUploadService;
use App\Services\FileResolverService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FolderController extends Controller
{
    public function __construct(
        protected FolderRepositoryInterface $folderRepository,
        protected FileRepositoryInterface $fileRepository,
        protected ProjectRepositoryInterface $projectRepository,
        protected FileUploadService $fileUploadService,
        protected FileResolverService $fileResolverService,
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
     * @param UpdateFolderRequest $request
     * @param string $folderUuid
     * @return FolderResource|JsonResponse
     */
    public function update(UpdateFolderRequest $request, string $folderUuid): FolderResource|JsonResponse
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

        $files = $this->fileRepository->getBy('folder_id', $folder->id);
        foreach ($files as $file) {
            $this->fileUploadService->deleteFile($file->path, 'public');
        }

        $this->folderRepository->delete($folder);

        return response()->json(['message' => 'Folder deleted successfully']);
    }

    /**
     * Get all files from a folder.
     *
     * @param Request $request
     * @param string $folderUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getFiles(Request $request, string $folderUuid): AnonymousResourceCollection|JsonResponse
    {
        $folder = $this->folderRepository->getByUuidOrFail($folderUuid);
        $fileResolver = $this->fileResolverService->resolve($request->segments(), $folderUuid);

        $files = $this->fileRepository->getAllByMorph($fileResolver['morph_type'], $fileResolver['morph_id']);

        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFileRequest $request
     * @param string $folderUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function storeFiles(StoreFileRequest $request, string $folderUuid): AnonymousResourceCollection|JsonResponse
    {
        $folder = $this->folderRepository->getByUuidOrFail($folderUuid);
        $files = $request->file('files');

        $fileResolver = $this->fileResolverService->resolve($request->segments(), $folderUuid);
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
}
