<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserInfoRepositoryInterface $infoRepository
    ) {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param string $uuid
     * @return Response|JsonResponse
     */
    public function update(UpdateUserRequest $request, string $uuid): Response|JsonResponse
    {
        try {
            $user = auth()->user();
            $this->authorize('update', $user);

            $validatedUserInput = $request->safe()->only([
                'name_first',
                'name_last',
                'email',
                'phone',
                'bookkeeping_started_at',
                'oss_registered_at',
            ]);

            $validatedInfoInput = $request->safe()->only([
                'phone',
                'website',
                'communication_language',
            ]);

            if (!empty($validatedUserInput['oss_registered_at'])) {
                $validatedUserInput['oss_registered_at'] = Carbon::parse($validatedUserInput['oss_registered_at'])->format('Y-m-d H:i:s');
            }

            $this->userRepository->update($user, $validatedUserInput);
            $this->infoRepository->update($user->info, $validatedInfoInput);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(200);
        } catch (Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating a user. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating a user. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
