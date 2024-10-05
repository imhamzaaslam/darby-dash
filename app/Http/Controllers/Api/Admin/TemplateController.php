<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\TemplateRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Resources\TemplateResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TemplateController extends Controller
{
    public function __construct(
        protected TemplateRepositoryInterface $templateRepository,
        protected ProjectRepositoryInterface $projectRepository,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        $templates = $this->templateRepository->get();
        return TemplateResource::collection($templates);
    }

    /**
     * Display a listing of the resource with pagination.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function templatesWithPagination(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $templates = $this->templateRepository
                ->getTemplatesQuery()
                ->filtered($request->keyword ?? '')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));
        }

        return TemplateResource::collection($templates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function store(StoreTemplateRequest $request, string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $validated = $request->validated();

        $attributes = [
            'template_name' => $validated['template_name'],
        ];

        $template = $this->templateRepository->create($attributes, $project);

        return (new TemplateResource($template))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function delete(string $templateUuid): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {

            $template = $this->templateRepository->getByUuidOrFail($templateUuid);

            $this->templateRepository->delete($template);

            return response()->json(['message' => 'Template deleted successfully']);

        }
    }
}
