<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectBucksRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\ProjectResource;
use App\Models\ProjectBucks;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProjectBucksController extends Controller
{
    public function __construct(
        protected ProjectBucksRepositoryInterface $projectBucksRepository,
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
        $project = $this->projectRepository->getByUuid($projectUuid);
        $this->authorize('viewbucks', $project);

        $bucks = $this->projectBucksRepository->index($project);
        
        // check if project bucks_share_type is fixed or percentage then find remaining bucks accordingly
        // if($project->bucks_share_type === 'fixed') {
        //     $remainingBucks = $project->bucks_share - $bucks->sum('bucks_share');
        // } else {
        //     $remainingBucks = $project->bucks_share - ($bucks->sum('bucks_share') * $project->budget_amount / 100);
        // }
        
        $remainingBucks = $project->bucks_share - $bucks->sum('bucks_share');

        $project = new ProjectResource($project);

        return response()->json([
            'project' => $project,
            'bucks' => $bucks,
            'remaining_bucks' => $remainingBucks,
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $projectUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function update(Request $request, string $projectUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuid($projectUuid);
        $this->authorize('updatebucks', $project);
        
        // Step 1: Validate the roleId
        $request->validate(['roleId' => 'required|exists:roles,id']);
        
        // Step 2: Calculate remainingBucks
        $roleShare = $this->projectBucksRepository->getRoleShare($project, $request->roleId);
        $bucks = $this->projectBucksRepository->index($project);
        $remainingBucks = $project->bucks_share - $bucks->sum('bucks_share') + $roleShare;
        
        // Step 3: Validate the shares field
        $request->validate(['shares' => 'required|numeric|min:0|max:' . $remainingBucks]);
        
        $data = $request->all();
        $this->projectBucksRepository->update($project, $data);
        
        return $this->index($projectUuid);
    }
}
