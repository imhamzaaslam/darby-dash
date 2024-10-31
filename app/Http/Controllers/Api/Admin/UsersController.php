<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\User\UpdateUserAdminRequest;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\FileResource;
use App\Services\FileResolverService;
use App\Services\FileUploadService;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Enums\Management;
use App\Enums\UserRole;

class UsersController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserInfoRepositoryInterface $userInfoRepository,
        protected FileRepositoryInterface $fileRepository,
        protected FileResolverService $fileResolverService,
        protected FileUploadService $fileUploadService,
        protected NotificationService $notificationService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $users = $this->userRepository
            ->getAllRecordsQuery()
            ->filtered($request->name ?? '', $request->email ?? '', $request->roleId ?? null)
            ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
            ->paginate($request->per_page ?? config('pagination.per_page', 10));

        return UserResource::collection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getAllUsers(): AnonymousResourceCollection|JsonResponse
    {
        $users = $this->userRepository->getAllRecordsQuery()->get();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest  $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $attributes = [
            'name_first' => $validated['name_first'],
            'name_last' => $validated['name_last'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'company_id' => Auth::user()->company_id,
        ];

        $role = $validated['role'];

        $infoAttributes = [
            'phone' => $validated['phone'] ?? '',
            'avatar' => $validated['avatar'] ?? null,
            'address' => $validated['address'] ?? '',
            'city' => $validated['city'] ?? '',
            'zip' => $validated['zip'] ?? '',
        ];

        $user = $this->userRepository->create($role, $attributes, $infoAttributes ?? []);

        //Send Notification
        $this->notificationService->sendNotification(Management::USER->value, 'user-created', $user->id, $user->toArray());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function getByRole(string $role, Request $request): AnonymousResourceCollection|JsonResponse
    {
        $users = $this->userRepository->getByRole($role);

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        $user = $this->userRepository->getByUuidOrFail($uuid);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserAdminRequest $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function update(UpdateUserAdminRequest $request, string $uuid): Response|JsonResponse
    {
        $validated = $request->validated();
        $user = $this->userRepository->getByUuidOrFail($uuid);

        $validatedUserInput = $request->safe()->only([
            'name_first',
            'name_last',
            'email',
            'password',
        ]);

        $validatedInfoInput = $request->safe()->only([
            'phone' => $validated['phone'] ?? '',
            'avatar' => $validated['avatar'] ?? $user->info->avatar,
            'address' => $validated['address'] ?? '',
            'city' => $validated['city'] ?? '',
            'zip' => $validated['zip'] ?? '',
        ]);

        $role = $validated['role'];

        $this->userRepository->update($role, $user, $validatedUserInput);
        $this->userInfoRepository->update($user->info, $validatedInfoInput);

        if ($user->hasRole(UserRole::ADMIN->value) && $request->has('company') && !empty($validated['company'])) {
            $companyName = $validated['company'];
            $user->company()->update([
                'name' => $companyName,
            ]);
        }

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAvatarRequest $request
     * @param  string  $uuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function updateImage(UpdateAvatarRequest $request, string $uuid): AnonymousResourceCollection|JsonResponse
    {
        $user = $this->userRepository->getByUuidOrFail($uuid);

        $file = $request->file('avatar');

        $fileResolver = $this->fileResolverService->resolve($request->segments(), $uuid);

        // check if user has an avatar
        $existingFile = $this->fileRepository->getByMorph($fileResolver['morph_type'], $fileResolver['morph_id']);

        $disk = app()->environment('testing') ? 'testing' : 'public';

        $fileData = $this->fileUploadService->uploadFile(
            $file,
            $fileResolver['directory'],
            $disk,
            $file->getClientOriginalName()
        );

        if ($existingFile) {
            $file = $this->fileRepository->update(
                $existingFile->uuid,
                $fileResolver['morph_type'],
                $fileResolver['morph_id'],
                $fileData
            );
            $this->fileUploadService->deleteFile($existingFile->path, $disk);
        } else {
            $file = $this->fileRepository->store(
                $fileResolver['morph_type'],
                $fileResolver['morph_id'],
                $fileData
            );
        }

        return (new FileResource($file))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function delete(string $uuid): JsonResponse
    {
        $user = $this->userRepository->getByUuidOrFail($uuid);

        $this->userRepository->delete($user);

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    // updatePassword
    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePasswordRequest $request
     * @param  string  $uuid
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request, string $uuid): JsonResponse
    {
        $validated = $request->validated();
        $user = $this->userRepository->getByUuidOrFail($uuid);

        if (!password_verify($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The provided password does not match your current password',
                'errors' => [
                    'current_password' => ['The provided password does not match your current password'],
                ],
            ], 422);
        }

        $this->userRepository->updatePassword($user, $validated['new_password']);

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    public function update2FA(Request $request, string $uuid): JsonResponse
    {
        $user = $this->userRepository->getByUuidOrFail($uuid);

        $this->userRepository->update2FA($user, $request->isEnable);
        return response()->json([
            'message' => '2FA updated successfully',
        ]);
    }
}
