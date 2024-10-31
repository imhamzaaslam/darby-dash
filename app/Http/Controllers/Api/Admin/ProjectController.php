<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\TemplateRepositoryInterface;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\project\StoreProjectRequest;
use App\Http\Requests\project\UpdateProjectUsersRequest;
use App\Models\Project;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Services\ActivityService;
use App\Enums\Management;
use App\Enums\ActionType;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository,
        protected UserRepositoryInterface $userRepository,
        protected TemplateRepositoryInterface $templateRepository,
        protected NotificationService $notificationService,
        protected ActivityService $activityService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $user = Auth::user();
        $this->authorize('viewAll', Project::class);

        if ($user->hasRole(UserRole::ADMIN->value)) {
            $projects = $this->projectRepository
                ->getAllRecordsQuery()
                ->filtered($request->keyword ?? '', $request->projectTypeId ?? null, $request->projectManagerId ?? null)
                ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));
        } else {
            $projects = $this->projectRepository
                ->getUserProjectsQuery($user)
                ->filtered($request->keyword ?? '', $request->projectTypeId ?? null, $request->projectManagerId ?? null)
                ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
                ->paginate($request->per_page ?? config('pagination.per_page', 10));
        }

        return ProjectResource::collection($projects);
    }

    /**
     * Display a listing of the resource by project type.
     *
     * @param string $prjectTypId
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getByType(string $prjectTypId): AnonymousResourceCollection|JsonResponse
    {
        $projects = $this->projectRepository->getBy('project_type_id', $prjectTypId);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectRequest  $request
     * @return JsonResponse
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);
        $validated = $request->validated();

        $member_ids = [];
        if(isset($validated['client_id'])) {
            $member_ids[] = $validated['client_id'];
            unset($validated['client_id']);
        }
        if(isset($validated['project_manager_id'])) {
            $member_ids[] = $validated['project_manager_id'];
            unset($validated['project_manager_id']);
        }
        if(isset($validated['staff_ids'])) {
            $member_ids = array_merge($member_ids, $validated['staff_ids']);
            unset($validated['staff_ids']);
        }

        $project = $this->projectRepository->create($validated);
        if (count($member_ids) > 0) {
            $this->projectRepository->storeProjectMembers($project, $member_ids);
        }

        //save project lists and tasks from template
        $templateId = $validated['template_id'] ?? null;

        if ($templateId) {
            $template = $this->templateRepository->getTemplate($templateId);
            if ($this->templateRepository->hasTemplateLists($template) === 0) {
                $this->projectRepository->createBacklogList($project);
            }
            $this->templateRepository->createProjectListAndTask($template, $project);
        } else {
            $this->projectRepository->createBacklogList($project);
        }

        //Send notification & create activity
        $this->notificationService->sendNotification(Management::PROJECT->value, 'project-created', $member_ids, $project->toArray());
        $this->activityService->logActivity(Management::PROJECT, ActionType::CREATED, $project->id, $project->toArray(), $project->uuid);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return ProjectResource|JsonResponse
     */
    public function show(string $uuid): ProjectResource|JsonResponse
    {
        $project = $this->projectRepository->getByUuid($uuid);
        $this->authorize('view', $project);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreProjectRequest  $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(StoreProjectRequest $request, string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuid($uuid);
        $this->authorize('update', $project);
        $validated = $request->validated();

        $member_ids = [];
        if(isset($validated['client_id'])) {
            $member_ids[] = $validated['client_id'];
            unset($validated['client_id']);
        }
        if(isset($validated['project_manager_id'])) {
            $member_ids[] = $validated['project_manager_id'];
            unset($validated['project_manager_id']);
        }
        if(isset($validated['staff_ids'])) {
            $member_ids = array_merge($member_ids, $validated['staff_ids']);
            unset($validated['staff_ids']);
        }


        $this->projectRepository->update($project, $validated);
        $this->projectRepository->updateProjectMembers($project, $member_ids);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }

    public function projectCompleted(Request $request, string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuid($uuid);
        $this->authorize('update', $project);
        $data = $request->all();
        $data['completed_at'] = $request->has('is_completed') && $request->is_completed == 1 ? now() : null;
        $this->projectRepository->update($project, $data);

        //Send notification & create activity
        if($request->has('is_completed') && $request->is_completed == 1)
        {
            $member_ids = $project->members->pluck('id');
            $this->notificationService->sendNotification(Management::PROJECT->value, 'project-completed', $member_ids, $project->toArray());
            $this->activityService->logActivity(Management::PROJECT, ActionType::COMPLETED, $project->id, $project->toArray(), $project->uuid);
        }

        //Send notification & create activity
        if($request->has('is_pm_bucks_awarded'))
        {
            $projectManager = $project->projectManager();
            if($projectManager)
            {
                $projectData = [
                    'amount' => $project->pm_bucks,
                    'title' => $project->title,
                ];
                $this->notificationService->sendNotification(Management::BUCKS->value, 'bucks-award', $projectManager->id, $projectData);
                $this->activityService->logActivity(Management::BUCKS, ActionType::AWARDED, $project->id, $projectData, $project->uuid);
            }
        }

        return (new ProjectResource($project))
        ->response()
        ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function delete(string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('delete', $project);

        //Send Notification
        $member_ids = $project->members->pluck('id');
        $this->notificationService->sendNotification(Management::PROJECT->value, 'project-deleted', $member_ids, $project->toArray());

        $this->projectRepository->delete($project);

        return response()->json(['message' => 'Project deleted successfully']);
    }

    /**
     * Get project users.
     *
     * @param Request $request
     * @param string $uuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function users(Request $request, string $uuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('view', $project);

        $users = $this->projectRepository
            ->getProjectMembersQuery($project)
            ->filtered($request->name ?? '', $request->email ?? '', $request->roleId ?? null)
            ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
            ->paginate($request->per_page ?? config('pagination.per_page', 10));

        return UserResource::collection($users);
    }

    /**
     * Get project users.
     *
     * @param string $uuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function allUsers(string $uuid): AnonymousResourceCollection|JsonResponse
    {
        $project = $this->projectRepository->getByUuid($uuid);
        $this->authorize('view', $project);

        return UserResource::collection($project->users);
    }

    /**
     * Update project users.
     *
     * @param UpdateProjectUsersRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function updateUsers(UpdateProjectUsersRequest $request, string $uuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('update', $project);

        $member_ids = $request->member_ids;

        $this->projectRepository->updateProjectMembers($project, $member_ids);

        //Send notification & create activity
        $this->notificationService->sendNotification(Management::MEMBER->value, 'memeber-created', $member_ids, $project->toArray());

        foreach ($member_ids as $memberID) {
           $this->activityService->logActivity(Management::MEMBER, ActionType::CREATED, $memberID, $project->toArray(), $project->uuid);
        }

        return response()->json(['message' => 'Members updated successfully']);
    }

    /**
     * Remove the specified user from project.
     *
     * @param string $uuid
     * @param string $userUuid
     * @return JsonResponse
     */
    public function deleteUser(string $uuid, string $userUuid): JsonResponse
    {
        $project = $this->projectRepository->getByUuidOrFail($uuid);
        $this->authorize('update', $project);
        $user = $this->userRepository->getByUuidOrFail($userUuid);

        $this->projectRepository->deleteProjectMember($project, $user);

        //Send notification & create activity
        $this->notificationService->sendNotification(Management::MEMBER->value, 'memeber-deleted', $user->id, $project->toArray());

        $this->activityService->logActivity(Management::MEMBER, ActionType::DELETED, $user->id, $project->toArray(), $project->uuid);

        return response()->json(['message' => 'Member removed successfully']);
    }
}
