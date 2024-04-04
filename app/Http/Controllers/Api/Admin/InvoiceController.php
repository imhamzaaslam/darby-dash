<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\InvoiceRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected InvoiceRepositoryInterface $invoiceRepository
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', auth()->user());

        try {
            $invoices = $this->invoiceRepository
                ->getAllRecordsQuery()
                ->ordered()
                ->paginate($request->per_page ?? config('pagination.per_page', 10));

            return InvoiceResource::collection($invoices);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'error' => 'Something went wrong while getting the invoices. Please try again later.'
            ], 500);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $invoice = $this->invoiceRepository->getByUuidOrFail($uuid);

        try {
            $this->authorize('view', auth()->user());

            return (new InvoiceResource($invoice))
                ->response()
                ->setStatusCode(200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'No such invoice: ' . $uuid,
            ], 404);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'error' => "Something went wrong while getting invoice $uuid. Please try again later."
            ], 500);
        }
    }
}
