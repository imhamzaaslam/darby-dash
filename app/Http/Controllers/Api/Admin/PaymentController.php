<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\PaymentRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository,
        protected ProjectRepositoryInterface $projectRepository
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
        try {
            $project = $this->projectRepository->getByUuidOrFail($projectUuid);

            $payments = $this->paymentRepository
                ->getRecordsQuery('project_id', $project->id)
                ->filtered($request->keyword ?? '')
                ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));
            
            return PaymentResource::collection($payments);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePaymentRequest  $request
     * @param string $projectUuid
     * @return JsonResponse
     */
    public function store(StorePaymentRequest $request, string $projectUuid): JsonResponse
    {
        try {
            $project = $this->projectRepository->getByUuidOrFail($projectUuid);
            $payment = $this->paymentRepository->create($project, $request->validated());
            
            return (new PaymentResource($payment))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
