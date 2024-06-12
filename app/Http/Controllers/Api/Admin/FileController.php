<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\FileRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\FolderRepositoryInterface;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Services\FileResolverService;
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
        protected FileResolverService $fileResolverService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request, string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $fileResolver = $this->fileResolverService->resolve($request->segments(), $projectUuid);

        $files = $this->fileRepository->getAllByMorph($fileResolver['morph_type'], $fileResolver['morph_id']);

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

        $fileResolver = $this->fileResolverService->resolve($request->segments(), $projectUuid);
        $disk = app()->environment('testing') ? 'testing' : 'public';

        $storedFiles = [];
        foreach ($files as $file) {
            $fileData = $this->fileUploadService->uploadFile(
                $file,
                $fileResolver['directory'],
                $disk,
                $file->getClientOriginalName()
            );

            $fileData = $this->fileUploadService->uploadFile($file, 'uploads', 'public');

            $storedFiles[] = $this->fileRepository->store(
                $fileResolver['morph_type'],
                $fileResolver['morph_id'],
                $fileData
            );
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
}
