<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\User\UpdateUserAdminRequest;
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
        try {
            $this->authorize('view', auth()->user());
    
            $users = $this->userRepository
                ->hasInfo()
                ->filtered($request->keyword ?? '')
                ->ordered($request->orderBy ?? 'id', $request->order ?? 'asc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));

            return UserResource::collection($users);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'You do not have the permissions to access this page.'
            ], 403);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting users. Exception: {$e->getMessage()}");
            Log::warning("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting users. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while getting the users. Please try again later.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest  $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $attributes = [
                'name_first' => $validated['name_first'],
                'name_last' => $validated['name_last'],
                'email' => $validated['email'],
            ];

            $role = $validated['role'];

            if ($role === 'customer') {
                $infoAttributes = [
                    'company' => $validated['company'],
                    'coc_number' => $validated['coc_number'],
                ];
            }

            if ($role === 'admin') {
                $infoAttributes = [
                    'company' => config('app.name'),
                    'website' => config('app.corporate_url'),
                    'country' => 'NL',
                    'communication_language' => config('app.locale'),
                ];
            }

            $user = $this->userRepository->create($role, $attributes, $infoAttributes ?? []);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(201);
        } catch (Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while creating a user. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while creating a user. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while creating the user. Please try again later.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $this->authorize('view', auth()->user());

            $user = $this->userRepository->getByUuidOrFail($uuid);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'You do not have the permissions to access this page.'
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting a user by uuid: {$uuid}. Exception: {$e->getMessage()}");
            Log::warning("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting a user by uuid: {$uuid}. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while creating the user. Please try again later.'
            ], 500);
        }
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
        try {
            $this->authorize('update', auth()->user());
            $user = $this->userRepository->getByUuidOrFail($uuid);

            $validatedUserInput = $request->safe()->only([
                'name_first',
                'name_last',
                'email',
                'phone',
                'yuki_access_key',
                'bookkeeping_started_at',
                'oss_registered_at',
                'state'
            ]);

            $validatedInfoInput = $request->safe()->only([
                'company',
                'coc_number',
                'phone',
                'website',
                'communication_language'
            ]);

            if (!empty($validatedUserInput['oss_registered_at'])) {
                $validatedUserInput['oss_registered_at'] = Carbon::parse($validatedUserInput['oss_registered_at'])->format('Y-m-d H:i:s');
            }

            $this->userRepository->update($user, $validatedUserInput);
            $this->userInfoRepository->update($user->info, $validatedInfoInput);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(200);
        } catch (Exception $e) {
            activity('error')
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating a user. Exception: {$e->getMessage()}");
            report($e);
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
