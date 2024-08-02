<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\MileStoneRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\MileStoneResource;
use App\Http\Requests\project\StoreMileStoneRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MileStoneController extends Controller
{
    public function __construct(
        protected MileStoneRepositoryInterface $mileStoneRepository,
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * Request $request
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request, string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('delete', $project);
        $mileStones = $this->mileStoneRepository
            ->getByProjectQuery($project)
            ->paginate($request->per_page ?? config('pagination.per_page', 10));

        return MileStoneResource::collection($mileStones);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMileStoneRequest $request
     * @param string $projectUuid
     * @return JsonResponse
     */
    public function store(StoreMileStoneRequest $request, string $projectUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('create', $project);
        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
        ];

        $res = $this->mileStoneRepository->create($project, $payload);
        $this->mileStoneRepository->syncProjectLists($res, $validated['project_list_ids']);

        $mileStone = $this->mileStoneRepository->getByUuidOrFail($res->uuid);

        return (new MileStoneResource($mileStone))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     * @param StoreMileStoneRequest $request
     * @param string $mileStoneUuid
     * @return JsonResponse
     */
    public function update(StoreMileStoneRequest $request, string $mileStoneUuid): JsonResponse
    {
        $mileStone = $this->mileStoneRepository->getByUuidOrFail($mileStoneUuid);
        $project = $mileStone->project;
        $this->authorize('update', $project);
        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
        ];

        $this->mileStoneRepository->update($mileStone, $payload);
        $this->mileStoneRepository->syncProjectLists($mileStone, $validated['project_list_ids']);

        return (new MileStoneResource($mileStone))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $mileStoneUuid
     * @return JsonResponse
     */
    public function delete(string $mileStoneUuid): JsonResponse
    {
        $mileStone = $this->mileStoneRepository->getByUuidOrFail($mileStoneUuid);
        $project = $mileStone->project;
        $this->authorize('delete', $project);
        $this->mileStoneRepository->delete($mileStone);

        return response()->json(['message' => 'MileStone deleted successfully'], 200);
    }
}
