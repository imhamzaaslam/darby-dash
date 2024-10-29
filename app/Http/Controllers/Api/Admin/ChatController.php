<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ChatRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\ChatResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatController extends Controller
{
    public function __construct(
        protected ChatRepositoryInterface $chatRepository,
        protected ProjectRepositoryInterface $projectRepository,
        protected UserRepositoryInterface $userRepository,
        ) {}

    /**
     * Display a listing of the resource.
     * @param string $uuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function chatsContacts(string $uuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $contacts = $project->members;
        $chats = $this->chatRepository->getChatsByProject($project);

        return response()->json([
            'success' => true,
            'contacts' => UserResource::collection($contacts),
            'chats' => ChatResource::collection($chats),
        ]);
    }

    /**
     * Display a listing of the resource.
     * @param string $uuid
     * @param string $userUuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function show(string $uuid, string $userUuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $user = $this->userRepository->getByUuidOrFail($userUuid);
        $chat = $this->chatRepository->getChatByUser($user, $project);
        if ($chat->isEmpty()) {
            $placeholderChat = $this->chatPlaceHolderData($user);
            return response()->json([
                'success' => true,
                'chat' => $placeholderChat,
            ]);
        }
        else{
            return response()->json([
                'success' => true,
                'chat' => ChatResource::collection($chat),
            ]);
        }
    }

    public function chatPlaceHolderData($user)
    {
        $userData = [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'name_first' => $user->name_first,
            'name_last' => $user->name_last,
            'email' => $user->email,
            'role' => ucwords($user->getRoleNames()->first() ?? ''),
            'state' => $user->state,
            'phone' => $user->info->phone,
            'is_2fa' => $user->is_2fa,
            'is_online' => $user->isOnline(),
            'company' => $user->company->name ?? 'N/A',
            'info' => [
                'phone' => $user->info->phone,
                'address' => $user->info->address,
                'city' => $user->info->city,
                'state' => $user->info->state,
                'zip' => $user->info->zip,
                'avatar' => $user->avatar,
            ],
            'created_at' => Carbon::parse($user->created_at)->format('d/m/Y h:i:s A'),
        ];
        $placeholderChat = [
            'id' => null,
            'uuid' => null,
            'user_id' => $user->id,
            'unseen_msgs' => 0,
            'contact' => $userData,
            'messages' => collect([])->map(function () {
                return [
                    'id' => null,
                    'uuid' => null,
                    'senderId' => null,
                    'message' => null,
                    'created_at' => null,
                    'feedback' => [
                        'isSeen' => false,
                        'isDelivered' => false,
                        'isSent' => false,
                    ],
                ];
            }),
        ];

        return $placeholderChat;
    }
}
