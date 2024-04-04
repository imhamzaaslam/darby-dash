<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\File;
use App\Http\Resources\EntitiesResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EntitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */

    public function index(): AnonymousResourceCollection | JsonResponse
    {
        try {
            // Get the path to the app/Models directory
            $modelsPath = app_path('Models');

            // Get all PHP files in the models directory
            $modelFiles = File::files($modelsPath);

            // Loop through each file and extract the class name
            $models = [];
            foreach ($modelFiles as $file) {
                $className = File::name($file);
                $models[] = $className;
            }
            return EntitiesResource::collection([
                'entities' => $models
            ]);
        } catch (\Exception $e) {
            activity()->log("Error while getting activity logs: {$e->__toString()}");
            Log::error("Error in " . __CLASS__ . " on line " . __LINE__ . " while getting activity logs. Exception: {$e->getMessage()}");
            return response()->json([
                'error' => 'Something went wrong while getting activity logs. Please try again later.'
            ], 500);
        }
    }
}
