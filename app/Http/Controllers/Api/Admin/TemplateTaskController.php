<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\TemplateTaskRepositoryInterface;
use App\Contracts\TemplateListRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TemplateTaskController extends Controller
{
    public function __construct(
        protected TemplateTaskRepositoryInterface $templateTaskRepository,
        protected TemplateListRepositoryInterface $templateListRepository,
    ) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $listUuid
     * @return JsonResponse
     */
    public function store(Request $request, string $listUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $list = $this->templateListRepository->getByUuidOrFail($listUuid);
            $attributes = $request->all();

            $this->templateTaskRepository->create($attributes, $list);

            return response()->json(['message' => 'Template Task added successfully']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $taskUuid
     * @return JsonResponse
     */
    public function update(Request $request, string $taskUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $task = $this->templateTaskRepository->getByUuid($taskUuid);
            $attributes = $request->all();
            $this->templateTaskRepository->update($task, $attributes);

            return response()->json(['message' => 'Template Task update successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $taskUuid
     * @return JsonResponse
     */
    public function delete(string $taskUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN->value)) {

            $task = $this->templateTaskRepository->getByUuidOrFail($taskUuid);

            $this->templateTaskRepository->delete($task);

            return response()->json(['message' => 'Template task deleted successfully']);

        }
    }
}
