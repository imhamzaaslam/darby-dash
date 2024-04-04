<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\ArticleCategoryRepositoryInterface;
use App\Contracts\ArticleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\DeleteArticleRequest;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ArticlesController extends Controller
{
    public function __construct(
        protected ArticleRepositoryInterface $articleRepository,
        protected  ArticleCategoryRepositoryInterface $articleCategoryRepository
    ) {
    }

    public function index(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $articleCategories = $this->articleRepository->getAll();

            if (request()->query('category')) {
                $articleCategories = $this->articleRepository->getBy('category_id', request()->query('category'));
            }
            return ArticleResource::collection($articleCategories);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting articles. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting articles. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while getting the articles. Please try again later.'
            ], 500);
        }
    }

    public function store(StoreArticleRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $validated = $request->validated();

        try {
            $articleCategory = $this->articleCategoryRepository->getFirstBy('id', $validated['category_id']);
            $article = $this->articleRepository->create($articleCategory, $validated['title'], $validated['content']);
            return (new ArticleResource($article))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while posting an article. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while posting an article. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while posting the article. Please try again later.'
            ], 500);
        }
    }

    public function update(UpdateArticleRequest $request, Article $article): JsonResponse|AnonymousResourceCollection
    {
        $validated = $request->validated();

        try {
            $this->articleRepository->update($article, $validated);
            $article->refresh();
            return (new ArticleResource($article))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating the article. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while updating the article. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while updating the article. Please try again later.'
            ], 500);
        }
    }

    public function delete(DeleteArticleRequest $request, Article $article): JsonResponse|AnonymousResourceCollection
    {
        try {
            $this->articleRepository->delete($article);
            return response()->json([
                'message' => 'Article successfully deleted',
            ]);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while deleting the article. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while deleting the article. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while deleting the article. Please try again later.'
            ], 500);
        }
    }

    public function show(Article $article): JsonResponse|AnonymousResourceCollection
    {
        try {
            return (new ArticleResource($article))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            activity('error')
                ->causedBy(auth()->user())
                ->log("Error in " . __CLASS__ . " on line " . __LINE__ . " while retrieving the article. Exception: {$e->getMessage()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while retrieving the article. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while retrieving the article. Please try again later.'
            ], 500);
        }
    }
}
