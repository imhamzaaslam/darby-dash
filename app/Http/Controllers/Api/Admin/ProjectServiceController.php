<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectServiceRepositoryInterface;
use App\Contracts\ProjectTypeRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Http\Resources\ProjectServiceResource;
use App\Http\Resources\FileResource;
use App\Services\FileResolverService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\FileType;
use App\Enums\UserRole;
use App\Models\File;
use App\Models\ProjectService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectServiceController extends Controller
{
    public function __construct(
        protected ProjectServiceRepositoryInterface $projectServiceRepository,
        protected ProjectTypeRepositoryInterface $projectTypeRepository,
        protected FileRepositoryInterface $fileRepository,
        protected FileResolverService $fileResolverService,
        protected FileUploadService $fileUploadService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $services = $this->projectServiceRepository
                ->getServicesQuery()
                ->filtered($request->projectTypeId ?? null)
                ->ordered($request->orderBy ?? 'display_order', $request->order ?? 'asc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));

                return ProjectServiceResource::collection($services);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function servicesWithoutPaginaton(): AnonymousResourceCollection|JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $services = $this->projectServiceRepository
            ->getServicesQuery()
            ->ordered('display_order')
            ->get();
            return ProjectServiceResource::collection($services);
        }
    }

    /**
     * Display a listing of the resource by project type.
     *
     * @param string $projectTypeUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getByType(string $projectTypeUuid): AnonymousResourceCollection|JsonResponse
    {
        $projectType = $this->projectTypeRepository->getByUuidOrFail($projectTypeUuid);
        $services = $this->projectServiceRepository->getByType($projectType);

        return ProjectServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $attributes = $request->all();
            $service = $this->projectServiceRepository->create($attributes);

            if ($request->hasFile('serviceImage')) {
                $file = $request->file('serviceImage');
                $disk = app()->environment('testing') ? 'testing' : 'public';
                $fileData = $this->fileUploadService->uploadFile(
                    $file,
                    'project_service_images',
                    $disk,
                    $file->getClientOriginalName()
                );
                File::create([
                    'fileable_type' => ProjectService::class,
                    'fileable_id' => $service->id,
                    'path' => $fileData->path,
                    'type' => FileType::AVATAR->value,
                    'name' => $fileData->name,
                    'size' => $fileData->size,
                    'url' => $fileData->url,
                    'mime_type' => $fileData->mimeType,
                ]);
            }

            return response()->json(['message' => 'Project service added successfully']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $serviceUuid
     * @return ProjectServiceResource|JsonResponse
     */
    public function show(string $serviceUuid): ProjectServiceResource|JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $service = $this->projectServiceRepository->getByUuid($serviceUuid);

            return (new ProjectServiceResource($service))
                ->response()
                ->setStatusCode(200);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $serviceUuid
     * @return JsonResponse
     */
    public function update(Request $request, string $serviceUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $service = $this->projectServiceRepository->getByUuid($serviceUuid);
            $attributes = $request->all();
            $this->projectServiceRepository->update($service, $attributes);

            $file = $request->file('serviceImage');
            if ($file) {
                $disk = app()->environment('testing') ? 'testing' : 'public';
                $existingFile = $service->serviceImage;
                if ($existingFile) {
                  $this->fileUploadService->deleteFile($existingFile->path, $disk);
                }
                $fileData = $this->fileUploadService->uploadFile(
                    $file,
                    'project_service_images',
                    $disk,
                    $file->getClientOriginalName()
                );
                if ($existingFile) {
                    $existingFile->update([
                        'path' => $fileData->path,
                        'name' => $fileData->name,
                        'size' => $fileData->size,
                        'url' => $fileData->url,
                        'mime_type' => $fileData->mimeType,
                    ]);
                } else {
                    File::create([
                        'fileable_type' => ProjectService::class,
                        'fileable_id' => $service->id,
                        'path' => $fileData->path,
                        'type' => FileType::AVATAR->value,
                        'name' => $fileData->name,
                        'size' => $fileData->size,
                        'url' => $fileData->url,
                        'mime_type' => $fileData->mimeType,
                    ]);
                }
            }

            return response()->json(['message' => 'Project service update successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $serviceUuid
     * @return JsonResponse
     */
    public function delete(string $serviceUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {

            $list = $this->projectServiceRepository->getByUuidOrFail($serviceUuid);

            $this->projectServiceRepository->delete($list);

            return response()->json(['message' => 'Project service deleted successfully']);

        }
    }

    /**
     * Sort servcies.
     *  @param Request $request
     * @return JsonResponse
     */
    public function sortServices(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $this->projectServiceRepository->sortServices($request->input('services'));

            return response()->json(['message' => 'Project services sorted successfully']);
        }
    }
}
