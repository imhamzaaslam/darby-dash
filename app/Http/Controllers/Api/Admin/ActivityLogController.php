<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\ActivityLogRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogRepositoryInterface $activityLogRepository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $uuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(string $uuid): AnonymousResourceCollection | JsonResponse
    {
        try {

            $activityLogs = $this->activityLogRepository->get($uuid);
            return ActivityLogResource::collection($activityLogs);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'error' => 'Something went wrong while getting recent activity logs. Please try again later.'
            ], 500);
        }
    }
}
