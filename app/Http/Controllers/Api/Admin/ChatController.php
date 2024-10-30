<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ChatRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\chat\StoreChatMessageRequest;
use Illuminate\Http\Request;
use App\Http\Resources\ChatResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Models\User;
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
        $authUser = auth()->user();
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('view', $project);
        $search = request()->query('search', '');
        $contacts = $project->members;

        $superAdmin = User::where('company_id', $authUser->company_id)->whereHas('roles', function ($query) {
            $query->where('name', 'Super Admin');
        })->first();
        
        if ($superAdmin) {
            $contacts = $contacts->merge([$superAdmin]);
        }

        if ($search) {
            $contacts = $contacts->filter(function ($user) use ($search) {
                return stripos($user->name_first, $search) !== false ||
                       stripos($user->name_last, $search) !== false;
            });
        }

        $contacts = $contacts->filter(function ($user) {
            return $user->id !== auth()->id();
        });

        $chats = $this->chatRepository->getChatsByProject($project);

        if ($search) {
            $chats = $chats->filter(function ($chat) use ($search) {
                return $chat->contact && (
                    stripos($chat->contact->name_first, $search) !== false ||
                    stripos($chat->contact->name_last, $search) !== false
                );
            });
        }

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
     * @return JsonResponse
     */
    public function show(string $uuid, string $userUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('view', $project);
        $user = $this->userRepository->getByUuidOrFail($userUuid);
        $chat = $this->chatRepository->getChat($user, $project);
        if (!$chat) {
            $placeholderChat = $this->chatPlaceHolderData($user);
            return response()->json([
                'success' => true,
                'chat' => $placeholderChat,
            ]);
        }

        return response()->json([
            'success' => true,
            'chat' => new ChatResource($chat),
        ]);
    }

    /**
     * Store chat message by project.
     *
     * @param StoreChatMessageRequest $request
     * @param string $uuid
     * @param string $userUuid
     * @return JsonResponse
     */
    public function sendMessage(StoreChatMessageRequest $request, string $uuid, string $userUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);

        $this->authorize('view', $project);

        $validated = $request->validated();
        $user = $this->userRepository->getByUuidOrFail($userUuid);
        $chat = $this->chatRepository->getChat($user, $project);

        if (!$chat) {
            $chat = $this->chatRepository->createByProject($project, $user);
        }

        unset($validated['chatId']);
        $validated['chatId'] = $chat->id;

        $this->chatRepository->sendMessage($project, $user, $validated);

        $chat = $this->chatRepository->getChat($user, $project);

        return (new ChatResource($chat))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreChatMessageRequest  $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function updateMessage(StoreChatMessageRequest $request, string $uuid): JsonResponse
    {
        $validated = $request->validated();
        $chat = $this->chatRepository->getByUuidOrFail($uuid);

        $this->chatRepository->updateMessage($chat, $validated);

        $chat = $this->chatRepository->getByUuidOrFail($uuid);

        return (new ChatResource($chat))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function deleteMessage(Request $request, string $uuid): JsonResponse
    {
        $messageId = $request->messageId;
        $chat = $this->chatRepository->getByUuidOrFail($uuid);

        $this->chatRepository->deleteMessage($chat, $messageId);

        $chat = $this->chatRepository->getByUuidOrFail($uuid);

        return (new ChatResource($chat))
            ->response()
            ->setStatusCode(200);
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
