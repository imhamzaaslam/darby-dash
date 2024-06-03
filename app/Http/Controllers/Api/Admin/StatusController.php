<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\StatusRepositoryInterface;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StatusController extends Controller
{
    public function __construct(
        protected StatusRepositoryInterface $statusRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        $statuses = $this->statusRepository->getAll();
        return StatusResource::collection($statuses);
    }
}
