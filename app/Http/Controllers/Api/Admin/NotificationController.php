<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\NotificationRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepositoryInterface $notificationRepository
    ) {}

    /**
     * Display a listing of the resource.
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        $notifications = $this->notificationRepository->get();
        return NotificationResource::collection($notifications);
    }

    /**
     * Mark as Read notification
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function markAsRead(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $notificationIds = $request->input('ids', []);
        if (empty($notificationIds)) {
            return response()->json(['message' => 'No notification IDs provided'], 400);
        }

        foreach ($notificationIds as $id) {
            $notification = $this->notificationRepository->getNotification($id);
            if ($notification) {
                $this->notificationRepository->updateNotification($notification, ['read_at' => now()]);
            }
        }

        $notifications = $this->notificationRepository->get();

        return NotificationResource::collection($notifications);
    }

    /**
     * Mark as Unread notification
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function markAsUnread(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $notificationIds = $request->input('ids', []);

        if (empty($notificationIds)) {
            return response()->json(['message' => 'No notification IDs provided'], 400);
        }

        foreach ($notificationIds as $id) {
            $notification = $this->notificationRepository->getNotification($id);
            if ($notification) {
                $this->notificationRepository->updateNotification($notification, ['read_at' => null]);
            }
        }

        $notifications = $this->notificationRepository->get();

        return NotificationResource::collection($notifications);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function delete(string $uuid): JsonResponse
    {
        $notification = $this->notificationRepository->getNotification($uuid);

        $this->notificationRepository->deleteNotification($notification);

        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
