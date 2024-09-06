<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\TemplateRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Resources\TemplateResource;
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
}
