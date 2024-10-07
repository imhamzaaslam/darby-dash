<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\TemplateListRepositoryInterface;
use App\Contracts\TemplateRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TemplateListController extends Controller
{
    public function __construct(
        protected TemplateListRepositoryInterface $templateListRepository,
        protected TemplateRepositoryInterface $templateRepository,
    ) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $templateUuid
     * @return JsonResponse
     */
    public function store(Request $request, string $templateUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $template = $this->templateRepository->getByUuidOrFail($templateUuid);
            $attributes = $request->all();

            $this->templateListRepository->create($attributes, $template);

            return response()->json(['message' => 'Template List added successfully']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $listUuid
     * @return JsonResponse
     */
    public function update(Request $request, string $listUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $list = $this->templateListRepository->getByUuid($listUuid);
            $attributes = $request->all();
            $this->templateListRepository->update($list, $attributes);

            return response()->json(['message' => 'Template List update successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function delete(string $listUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {

            $list = $this->templateListRepository->getByUuidOrFail($listUuid);

            $this->templateListRepository->delete($list);

            return response()->json(['message' => 'Template List deleted successfully']);

        }
    }

    /**
     * Sort lists.
     *  @param Request $request
     * @param string $templateUuid
     * @return JsonResponse
     */
    public function sortLists(Request $request, string $templateUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $template = $this->templateRepository->getByUuidOrFail($templateUuid);
            $this->templateListRepository->sortLists($request->input('lists'));

            return response()->json(['message' => 'Project List sorted successfully']);
        }
    }
}
