<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\JournalEntryRepositoryInterface;
use App\Contracts\JournalRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Enums\JournalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Journal\UpdateJournalRequest;
use App\Http\Requests\Journal\GetJournalRequest;
use App\Http\Requests\Journal\GetJournalCountRequest;
use App\Http\Resources\JournalResource;
use App\Models\Journal;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class JournalsController extends Controller
{
    public function __construct(
        protected JournalRepositoryInterface $journalRepository,
        protected JournalEntryRepositoryInterface $journalEntryRepository,
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function index(GetJournalRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $validatedData = $request->validated();
        $this->authorize('viewAny', Journal::class);
        try {
            $relations = $this->journalRepository->relations();
            $journals = $this->journalRepository
                ->queryWithRelations($relations)
                ->where('status', $validatedData['status'])
                ->filtered($request->keyword ?? '')
                ->ordered($request->orderBy ?? 'id', $request->order ?? 'asc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));

            return JournalResource::collection($journals);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function getJournalsCountsByStatus(GetJournalCountRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $this->authorize('viewAny', Journal::class);
        try {
            $statuses = str_contains($validatedData['statuses'], ',')
                ? array_map(fn($status) => JournalStatus::tryFrom($status), explode(',', $validatedData['statuses']))
                : JournalStatus::tryFrom($validatedData['statuses']);

            $journalsCount = $this->journalRepository->journalsCountByStatus($statuses);
            return response()->json($journalsCount);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function update(UpdateJournalRequest $request, string $uuid): JsonResponse
    {
        try {
            $journal = $this->journalRepository->getByUuidOrFail($uuid);
            $this->authorize('update', $journal);

            $validated = $request->safe()->only(['status']);

            if (!empty($validated['status'])) {
                $this->journalRepository->addStatus($journal, JournalStatus::tryFrom($validated['status']));
            }

            $this->journalRepository->update(
                $journal,
                $request->safe()->only([
                    'invoice_id',
                    'platform_id',
                    'administration_coc_number',
                    'administration_key',
                    'document_subject',
                    'project_key',
                    'project_code',
                    ]
                )
            );

            return (new JournalResource($journal))
                ->response()
                ->setStatusCode(200);
        } catch (ModelNotFoundException $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " Journal not found for uuid: {$uuid}. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " Journal not found for uuid: {$uuid}. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => "No such Journal: {$uuid}."
            ], 404);
        } catch (Exception $e) {
            activity('error')
                ->performedOn($journal)
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating journal. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating journal. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function getJournalsForUser(Request $request, string $uuid): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('view', Journal::class);
        $user = $this->userRepository->getByUuidOrFail($uuid);

        try {
            $relations = $this->journalRepository->relations();
            $journals = $this->journalRepository
                ->queryWithRelations($relations)
                ->whereIn('shop_id', $user->shops->pluck('id')->toArray())
                ->where('status', '!=', JournalStatus::CREATED->value)
                ->filtered($request->keyword ?? '')
                ->ordered($request->orderBy ?? 'id', $request->order ?? 'asc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));

            return JournalResource::collection($journals);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
