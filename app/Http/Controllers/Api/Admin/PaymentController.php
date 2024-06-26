<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $payments = $this->paymentRepository
                ->getAllRecordsQuery()
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
     * @return JsonResponse
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->create($request->validated());
            
            return (new PaymentResource($payment))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
