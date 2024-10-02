<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\CalendarEventRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Resources\CalendarEventResource;
use App\Http\Requests\project\StoreCalendarEventRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalendarEventController extends Controller
{
    public function __construct(
        protected CalendarEventRepositoryInterface $calendarEventRepository,
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
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('view', $project);
        $calendarEvents = $this->calendarEventRepository->getBy('project_id', $project->id);

        return CalendarEventResource::collection($calendarEvents);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCalendarEventRequest $request
     * @param string $projectUuid
     * @return JsonResponse
     */
    public function store(StoreCalendarEventRequest $request, string $projectUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('create', $project);
        $validated = $request->validated();

        $res = $this->calendarEventRepository->create($project, $validated);
        $this->calendarEventRepository->syncGuests($res, $validated['guests_ids']);

        $calendarEvent = $this->calendarEventRepository->getByUuidOrFail($res->uuid);

        $calendarEventData = array_merge($calendarEvent->toArray(), ['project_title' => $project->title]);

        $this->notificationService->sendNotification(Management::CALENDAR->value, 'new-event', $validated['guests_ids'], $calendarEventData);

        return (new CalendarEventResource($calendarEvent))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     * @param StoreCalendarEventRequest $request
     * @param string $projectUuid
     * @param string $calendarEventUuid
     * @return JsonResponse
     */
    public function update(StoreCalendarEventRequest $request, string $projectUuid, string $calendarEventUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('update', $project);
        $calendarEvent = $this->calendarEventRepository->getByUuidOrFail($calendarEventUuid);
        $validated = $request->validated();

        $this->calendarEventRepository->update($calendarEvent, $validated);
        $this->calendarEventRepository->syncGuests($calendarEvent, $validated['guests_ids']);

        return (new CalendarEventResource($calendarEvent))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $calendarEventUuid
     * @return JsonResponse
     */
    public function delete(string $projectUuid, string $calendarEventUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($projectUuid);
        $this->authorize('delete', $project);
        $calendarEvent = $this->calendarEventRepository->getByUuidOrFail($calendarEventUuid);
        $this->calendarEventRepository->delete($calendarEvent);

        return response()->json(['message' => 'Calendar Event deleted successfully'], 200);
    }
}
