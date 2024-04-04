<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\CategoryRepositoryInterface;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ){
        
    }

    public function index()
    {
        return $this->categoryRepository->childCategories();
    }
}
