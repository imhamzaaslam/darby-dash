<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\JournalEntryRepositoryInterface;
use App\Contracts\JournalRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Journal\Entry\DeleteEntryRequest;
use App\Http\Requests\Journal\Entry\UpdateJournalEntryRequest;
use App\Http\Requests\Journal\Entry\UpdateJournalEntriesRequest;
use App\Http\Resources\JournalResource;
use App\Models\JournalEntry;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class JournalEntriesController extends Controller
{
    public function __construct(
        protected JournalRepositoryInterface $journalRepository,
        protected JournalEntryRepositoryInterface $journalEntryRepository,
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function update(UpdateJournalEntryRequest $request, string $uuid, JournalEntry $entry): JsonResponse
    {
        try {
            $journal = $this->journalRepository->getByUuidOrFail($uuid);

            if ($entry->journal->uuid !== $journal->uuid) {
                throw new \InvalidArgumentException();
            }

            $this->authorize('update', $journal);

            $validated = $request->safe()->only([
                "entry_date",
                "amount",
            ]);

            $this->journalEntryRepository->update($entry, $validated);

            return (new JournalResource($journal->load(['shop.user.info', 'entries'])))
                ->response()
                ->setStatusCode(200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => "No such journal {$uuid} or entry {$entry->id}."
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => "Unable to update this resource."
            ], 401);
        } catch (Exception $e) {
            activity('error')
                ->performedOn($entry)
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating journal entry. Exception: {$e->getMessage()}");

            report($e);

            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function updateEntries(UpdateJournalEntriesRequest $request, string $journalUuid): JsonResponse
    {
        $journal = $this->journalRepository->getByUuidOrFail($journalUuid);
        $this->authorize('update', $journal);

        try {
            $validated = $request->safe()->only([
                "entry_date",
            ]);

            $this->journalEntryRepository->updateEntries($journal, $validated);

            $relations = $this->journalRepository->relations();
            $journalWithRelations = $this->journalRepository->queryWithRelations($relations)->find($journal->id);

            return (new JournalResource($journalWithRelations))
                ->response()
                ->setStatusCode(200);
        } catch (Exception $e) {
            activity('error')
                ->performedOn($journal)
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating journal entry date. Exception: {$e->getMessage()}");

            report($e);

            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function delete(DeleteEntryRequest $request, string $uuid, JournalEntry $entry): JsonResponse
    {
        try {
            $journal = $this->journalRepository->getByUuidOrFail($uuid);

            if ($entry->journal->uuid !== $journal->uuid) {
                throw new \InvalidArgumentException();
            }

            $this->journalEntryRepository->delete($entry);

            return response()->json([
                'message' => 'Journal entry successfully deleted',
            ], 204);
        } catch (Exception $e) {
            activity('error')
                ->performedOn($entry)
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while deleting journal entry. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while deleting journal entry. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
