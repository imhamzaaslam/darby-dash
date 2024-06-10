<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\User\UpdateUserAdminRequest;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserInfoRepositoryInterface $userInfoRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $users = $this->userRepository
            ->hasInfo()
            ->filtered($request->keyword ?? '')
            // ->where('email', '!=', config('app.admin.email'))
            ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
            ->paginate($request->perPage ?? config('pagination.perPage', 10));

        return UserResource::collection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getAllUsers(): AnonymousResourceCollection|JsonResponse
    {
        $users = $this->userRepository->all();

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
            'state' => $validated['state'],
        ];

        $role = $validated['role'];

        $infoAttributes = [
            'phone' => $validated['phone'] ?? '',
            'avatar' => $validated['avatar'] ?? null,
            'address' => $validated['address'] ?? '',
            'city' => $validated['city'] ?? '',
            'state' => $validated['state'] ?? '',
            'zip' => $validated['zip'] ?? '',
        ];

        $user = $this->userRepository->create($role, $attributes, $infoAttributes ?? []);

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
            'state',
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

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAvatarRequest $request
     * @param  string  $uuid
     * @return JsonResponse
     */
    public function updateImage(UpdateAvatarRequest $request, string $uuid): JsonResponse
    {
        $user = $this->userRepository->getByUuidOrFail($uuid);

        $avatarName = $this->userInfoRepository->saveFile($request->file('avatar'), 'images/avatars');
        $this->userInfoRepository->update($user->info, ['avatar' => $avatarName]);

        return response()->json([
            'message' => 'Avatar updated successfully',
        ]);
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
}
