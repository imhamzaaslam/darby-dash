<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\ArticleCategoryRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleCategoryRequest;
use App\Http\Resources\ArticleCategoryResource;
use Illuminate\Support\Facades\Log;

class ArticleCategoryController extends Controller
{
    public function __construct(protected ArticleCategoryRepositoryInterface $articleCategoryRepository)
    {
    }

    public function index()
    {
        try {
            $articleCategories = $this->articleCategoryRepository->getAll();
            return ArticleCategoryResource::collection($articleCategories);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting article categories. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting article categories. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while getting the platforms. Please try again later.'
            ], 500);
        }
    }

    public function store(StoreArticleCategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $articleCategory = $this->articleCategoryRepository->create($validated);

            return (new ArticleCategoryResource($articleCategory))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while creating article category. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while creating article category. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while creating the category. Please try again later.'
            ], 500);
        }
    }
}
