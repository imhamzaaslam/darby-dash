<?php

namespace App\Services;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\FolderRepositoryInterface;
use App\Contracts\TaskRepositoryInterface;
use App\Exceptions\InvalidResourceException;
use App\Policies\UserPolicy;

class FileResolverService
{
    use AuthorizesRequests;

    private array $routes = [
        'users' => UserRepositoryInterface::class,
        'projects' => ProjectRepositoryInterface::class,
        'folders' => FolderRepositoryInterface::class,
        'task' => TaskRepositoryInterface::class,
    ];

    public function __construct(
        protected UserPolicy $userPolicy
    ) {
    }

    /**
     * Gets array of request segments and returns file data.
     *
     * @param array $routeSegments
     * @param string $uuid
     * @return array
     */
    public function resolve(array $routeSegments, string $uuid): array
    {
        if (!in_array('users', $routeSegments, true) && !in_array('projects', $routeSegments, true) && !in_array('folders', $routeSegments, true) && !in_array('task', $routeSegments, true)) {
            throw new InvalidResourceException();
        }

        foreach ($this->routes as $key => $route) {
            if (in_array($key, $routeSegments, true)) {
                $repository = $this->getRepositoryName($key);
                $model = app($repository)->getByUuid($uuid);
                // $this->authorize('canUploadFile', $model);
                $directory = "{$key}/{$model->uuid}";
            }
        }

        $morphToId = $model->id;
        $morphToType = $model->getMorphClass();

        return [
            'morph_type' => $morphToType,
            'morph_id' => $morphToId,
            'directory' => $directory,
        ];
    }

    private function getRepositoryName($routeName) : string {
        return $this->routes[$routeName];
    }
}

?>
