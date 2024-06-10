<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\FileRepositoryInterface;
// use App\Http\Resources\MileStoneResource;
// use App\Http\Requests\project\StoreMileStoneRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FileController extends Controller
{
    public function __construct(
        protected FileRepositoryInterface $fileRepository
    ) {}
}
