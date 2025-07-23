<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CompanyRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Http\Resources\FileResource;
use App\Services\FileResolverService;
use App\Services\FileUploadService;     
use App\Services\TenantService;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\Company\StoreCompanyLogoRequest;
use App\Http\Requests\Company\StoreCompanyFaviconRequest;
use App\Http\Requests\Company\StoreCompanyThemeColorsRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\User\UpdateUserAdminRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;
use App\Enums\UserRole;
use App\Enums\FileType;
use App\Enums\Settings;
use App\Models\File;
use App\Models\Company;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Settings_meta;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserInfoRepositoryInterface $userInfoRepository,
        protected CompanyRepositoryInterface $companyRepository,
        protected FileRepositoryInterface $fileRepository,
        protected FileResolverService $fileResolverService,
        protected FileUploadService $fileUploadService,
        protected TenantService $tenantService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $companies = $this->companyRepository
            ->getAllRecordsQuery()
            ->filtered($request->name ?? '')
            ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
            ->whereNotIn('id', [1])
            ->paginate($request->per_page ?? config('pagination.per_page', 10));
            return CompanyResource::collection($companies);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyRequest  $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        if (Auth::user()->hasRole(UserRole::SUPER_ADMIN->value)) {
            $validated = $request->validated();

            $companyAttributes = [
                'name' => $validated['name'],
            ];

            $company = $this->companyRepository->create($companyAttributes);
            $subdomain = Str::slug($company->name, '-');
            $databaseName = Str::slug($company->name, '_'); 
       
            try {
                $tenant = Tenant::create([
                    'id' => $databaseName,
                    'data' => [
                        'company_id' => $company->id,
                    ],
                ]);

                $domainHost = env('APP_HOST', 'localhost');

                $tenant->domains()->create([
                    'domain' => "{$subdomain}.{$domainHost}",
                ]);

                $tenancy = app(Tenancy::class);
                $tenancy->initialize($tenant);

                $tenantCompanyDetails = $this->companyRepository->create($companyAttributes);

                Artisan::call('tenants:seed', [
                    '--class' => 'RolesAndPermissionsSeeder',
                    '--force' => true,
                ]);

                $adminAttributes = [
                    'name_first' => $validated['name_first'],
                    'name_last' => $validated['name_last'],
                    'email' => $validated['email'],
                    'password' => bcrypt($validated['password']),
                    'company_id' => $tenantCompanyDetails->id,
                ];

                $role = UserRole::ADMIN->value;
                $infoAttributes = [
                    'phone' => $validated['phone'] ?? '',
                ];

                $this->userRepository->create($role, $adminAttributes, $infoAttributes);
                
                Artisan::call('tenants:seed', [
                    '--class' => 'ProjectTypeSeeder',
                    '--force' => true,
                ]);
                
                Artisan::call('tenants:seed', [
                    '--class' => 'CalendarFilterSeeder',
                    '--force' => true,
                ]);
                
                Artisan::call('tenants:seed', [
                    '--class' => 'StatusSeeder',
                    '--force' => true,
                ]);
                
                Artisan::call('tenants:seed', [
                    '--class' => 'SettingSeeder',
                    '--force' => true,
                ]);

                Settings_meta::updateOrCreate([
                    'user_id' => null,
                    'setting_id' => Settings::GENERAL->value,
                    'key' => 'is_bucks_setting',
                    'value' => 1,
                ]);

                tenancy()->end();

            } catch (\Exception $e) {
                tenancy()->end();
                return response()->json(['message' => 'Tenant creation failed', 'error' => $e->getMessage()], 500);
            }

            return (new CompanyResource($company))
                ->response()
                ->setStatusCode(201);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCompanyRequest $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function update(UpdateCompanyRequest $request, string $uuid): Response|JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $validated = $request->validated();
            $company = $this->companyRepository->getByUuidOrFail($uuid);
            $user = $this->userRepository->getByUuidOrFail($validated['user_uuid']);
            $validatedUserInput = $request->safe()->only([
                'name_first',
                'name_last',
                'email',
                'password',
            ]);
    
            $validatedInfoInput = $request->safe()->only([
                'phone' => $validated['phone'] ?? '',
            ]);
    
            $role = UserRole::ADMIN->value;
            $this->companyRepository->update($company, $validated['name']);
            $this->userRepository->update($role, $user, $validatedUserInput);
            $this->userInfoRepository->update($user->info, $validatedInfoInput);
            $company = $this->companyRepository->getByUuidOrFail($uuid);
            return (new CompanyResource($company))
                ->response()
                ->setStatusCode(200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $uuid
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function show(string $uuid): AnonymousResourceCollection|JsonResponse
    {
        $company = $this->companyRepository->getByUuidOrFail($uuid);

        return (new CompanyResource($company))
        ->response()
        ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function delete(string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            $this->companyRepository->delete($company);
    
            return response()->json([
                'message' => 'Company deleted successfully',
            ]);
        }
    }

    /**
     * Upload logo.
     *
     * @param StoreCompanyLogoRequest $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function uploadLogo(StoreCompanyLogoRequest $request, string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return null;
                }
            }
    
            $file = $request->file('file');

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();
            }
            else{
                $tenantCompany = Company::where('name', $company->name)->orderBy('id', 'asc')->first();
            }

            if ($file && $tenantCompany) {
                $disk = app()->environment('testing') ? 'testing' : 'public';
                $existingFile = $tenantCompany->logo;
                if ($existingFile) {
                    $this->fileUploadService->deleteFile($existingFile->path, $disk);
                }
                $directory = Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) ? strtolower(str_replace(' ', '_', $tenantCompany->name)) . '/assets/logo' : '/assets/logo';
                $fileData = $this->fileUploadService->uploadFile(
                    $file,
                    $directory,
                    $disk,
                    $file->getClientOriginalName()
                );
                if ($existingFile) {
                    $existingFile->update([
                        'path' => $fileData->path,
                        'name' => $fileData->name,
                        'type' => FileType::AVATAR->value,
                        'size' => $fileData->size,
                        'url' => $fileData->url,
                        'mime_type' => $fileData->mimeType,
                    ]);
                } else {
                    if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value)){
                        File::on('tenant')->create([
                            'fileable_type' => Company::class,
                            'fileable_id' => $tenantCompany->id,
                            'path' => $fileData->path,
                            'type' => FileType::AVATAR->value,
                            'name' => $fileData->name,
                            'size' => $fileData->size,
                            'url' => $fileData->url,
                            'mime_type' => $fileData->mimeType,
                        ]);
                    }else{
                        File::create([
                            'fileable_type' => Company::class,
                            'fileable_id' => $tenantCompany->id,
                            'path' => $fileData->path,
                            'type' => FileType::AVATAR->value,
                            'name' => $fileData->name,
                            'size' => $fileData->size,
                            'url' => $fileData->url,
                            'mime_type' => $fileData->mimeType,
                        ]);
                    }
                }
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $this->tenantService->resetTenant();
            }

            return response()->json([
                'message' => 'Company logo uploaded successfully',
                'url' => $fileData->path,
            ]);
        }
    }

    /**
     * Upload logo.
     *
     * @param StoreCompanyFaviconRequest $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function uploadFavicon(StoreCompanyFaviconRequest $request, string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return null;
                }
            }
    
            $file = $request->file('file');

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();
            }
            else{
                $tenantCompany = Company::where('name', $company->name)->orderBy('id', 'asc')->first();
            }

            if ($file && $tenantCompany) {
                $disk = app()->environment('testing') ? 'testing' : 'public';
                $existingFile = $tenantCompany->favicon;
                if ($existingFile) {
                    $this->fileUploadService->deleteFile($existingFile->path, $disk);
                }
                $directory = Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) ? strtolower(str_replace(' ', '_', $tenantCompany->name)) . '/assets/favicon' : '/assets/favicon';
                $fileData = $this->fileUploadService->uploadFile(
                    $file,
                    $directory,
                    $disk,
                    $file->getClientOriginalName()
                );
                if ($existingFile) {
                    $existingFile->update([
                        'path' => $fileData->path,
                        'name' => $fileData->name,
                        'size' => $fileData->size,
                        'type' => FileType::DOC->value,
                        'url' => $fileData->url,
                        'mime_type' => $fileData->mimeType,
                    ]);
                } else {
                    if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value)){
                        File::on('tenant')->create([
                            'fileable_type' => Company::class,
                            'fileable_id' => $tenantCompany->id,
                            'path' => $fileData->path,
                            'type' => FileType::DOC->value,
                            'name' => $fileData->name,
                            'size' => $fileData->size,
                            'url' => $fileData->url,
                            'mime_type' => $fileData->mimeType,
                        ]);
                    }else{
                        File::create([
                            'fileable_type' => Company::class,
                            'fileable_id' => $tenantCompany->id,
                            'path' => $fileData->path,
                            'type' => FileType::DOC->value,
                            'name' => $fileData->name,
                            'size' => $fileData->size,
                            'url' => $fileData->url,
                            'mime_type' => $fileData->mimeType,
                        ]);
                    }
                }
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $this->tenantService->resetTenant();
            }
    
            return response()->json([
                'message' => 'Company favicon uploaded successfully',
                'url' => $fileData->path,
            ]);
        }
    }

    /**
     * Save colors.
     *
     * @param Request $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function saveDetails(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
        ]);
        if (Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value)) {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found.'
                ]);
            }
            
            if ($company->name === 'Darby Dash') {
                $company->update([
                    'display_name' => $request->display_name,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Company details updated successfully.',
                ]);
            } else {           
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to set tenant.'
                    ]);
                }

                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();

                if ($tenantCompany) {
                    $tenantCompany->update([
                        'display_name' => $request->display_name,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Company not found in tenant.'
                    ]);
                }

                $this->tenantService->resetTenant();

                return response()->json([
                    'success' => true,
                    'message' => 'Company details updated successfully.',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ]);
    }


    /**
     * Save bucks details.
     *
     * @param Request $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function saveBucksDetails(Request $request, string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return null;
                }
            }
    
            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();
            }
            else{
                $tenantCompany = Company::where('name', $company->name)->orderBy('id', 'asc')->first();
            }

            if($tenantCompany)
            {
                $settings = [
                    ['key' => 'bucks_label', 'value' => $request->label],
                    ['key' => 'is_bucks_setting', 'value' => $request->isBucksSetting],
                ];

                if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
                {
                    foreach ($settings as $setting) {
                        Settings_meta::on('tenant')->updateOrCreate(
                            [
                                'user_id' => null,
                                'setting_id' => Settings::GENERAL->value,
                                'key' => $setting['key'],
                            ],
                            [
                                'type' => 'string',
                                'value' => $setting['value'],
                            ]
                        );
                    }
                }else{
                    foreach ($settings as $setting) {
                        Settings_meta::updateOrCreate(
                            [
                                'user_id' => null,
                                'setting_id' => Settings::GENERAL->value,
                                'key' => $setting['key'],
                            ],
                            [
                                'type' => 'string',
                                'value' => $setting['value'],
                            ]
                        );
                    }
                }
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $this->tenantService->resetTenant();
            }

            return response()->json([
                'message' => 'Details saved successfully!'
            ]);
        }
    }

    /**
     * Save bucks details.
     *
     * @param Request $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function saveUiPrefrencesDetails(Request $request, string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return null;
                }
            }
    
            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();
            }
            else{
                $tenantCompany = Company::where('name', $company->name)->orderBy('id', 'asc')->first();
            }

            if($tenantCompany)
            {
                $settings = [
                    ['key' => 'is_magnifier_icon', 'value' => $request->isMagnifierIcon],
                ];

                if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
                {
                    foreach ($settings as $setting) {
                        Settings_meta::on('tenant')->updateOrCreate(
                            [
                                'user_id' => null,
                                'setting_id' => Settings::GENERAL->value,
                                'key' => $setting['key'],
                            ],
                            [
                                'type' => 'string',
                                'value' => $setting['value'],
                            ]
                        );
                    }
                }else{
                    foreach ($settings as $setting) {
                        Settings_meta::updateOrCreate(
                            [
                                'user_id' => null,
                                'setting_id' => Settings::GENERAL->value,
                                'key' => $setting['key'],
                            ],
                            [
                                'type' => 'string',
                                'value' => $setting['value'],
                            ]
                        );
                    }
                }
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $this->tenantService->resetTenant();
            }

            return response()->json([
                'message' => 'Details saved successfully!'
            ]);
        }
    }

    /**
     * Save colors.
     *
     * @param StoreCompanyThemeColorsRequest $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function saveColors(StoreCompanyThemeColorsRequest $request, string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return null;
                }
            }
    
            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();
            }
            else{
                $tenantCompany = Company::where('name', $company->name)->orderBy('id', 'asc')->first();
            }

            if($tenantCompany)
            {
                if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
                {
                    $setting = Settings_meta::on('tenant')->updateOrCreate(
                        [
                            'user_id' => null,
                            'setting_id' => Settings::GENERAL->value,
                            'key' => 'primary_color',
                        ],
                        [
                            'type' => 'string',
                            'value' => $request->primary_color,
                        ]
                    );
                }
                else{
                    $setting = Settings_meta::updateOrCreate(
                        [
                            'user_id' => null,
                            'setting_id' => Settings::GENERAL->value,
                            'key' => 'primary_color',
                        ],
                        [
                            'type' => 'string',
                            'value' => $request->primary_color,
                        ]
                    );
                }
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $this->tenantService->resetTenant();
            }

            return response()->json([
                'message' => 'Color setting saved successfully!'
            ]);
        }
    }

    /**
     * Update Active state.
     *
     * @param Request $request
     * @param  string  $uuid
     * @return Response|JsonResponse
     */
    public function updateActiveState(Request $request, string $uuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            $tenant = $this->tenantService->setTenant($company->name);

            if (!$tenant) {
                return null;
            }
    
            $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();

            if($tenantCompany)
            {
                $tenantCompany->update([
                    'status' => $request->isActive,
                ]);
            }

            $this->tenantService->resetTenant();

            return response()->json([
                'message' => 'State saved successfully!'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param string $fileUuid
     * @return JsonResponse
     */
    public function deleteAsset(Request $request, string $uuid ,string $fileUuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value) || Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenant = $this->tenantService->setTenant($company->name);

                if (!$tenant) {
                    return null;
                }
            }

            if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
            {
                $tenantCompany = Company::on('tenant')->where('name', $company->name)->orderBy('id', 'asc')->first();
            }
            else{
                $tenantCompany = Company::where('name', $company->name)->orderBy('id', 'asc')->first();
            }

            if($tenantCompany)
            {
                if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
                {
                    $file = File::on('tenant')->where('uuid', $fileUuid)->first();
                }
                else{
                    $file = File::where('uuid', $fileUuid)->first();
                }

                if($file)
                {
                    $this->fileUploadService->deleteFile($file->path, 'public');

                    $file->delete();
                } 
            }

            $this->tenantService->resetTenant();

            return response()->json(['message' => 'File deleted successfully']);
        }        
    }

    /**
     * Display a listing of the resource.
     * @param  string  $uuid
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getAllUsers(Request $request, string $uuid): AnonymousResourceCollection|JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            $tenant = $this->tenantService->setTenant($company->name);

            if (!$tenant) {
                return null;
            }

            $users = User::on('tenant')
            ->filtered($request->name ?? '', $request->email ?? '', $request->roleId ?? null)
            ->ordered($request->orderBy ?? 'id', $request->order ?? 'desc')
            ->paginate($request->per_page ?? config('pagination.per_page', 10));

            $this->tenantService->resetTenant();

            return UserResource::collection($users);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  string  $uuid
     * @param StoreUserRequest  $request
     * @return JsonResponse
     */
    public function storeUser(StoreUserRequest $request, string $uuid): JsonResponse
    {
        $validated = $request->validated();

        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            $tenant = $this->tenantService->setTenant($company->name);

            if (!$tenant) {
                return null;
            }

            $attributes = [
                'name_first' => $validated['name_first'],
                'name_last' => $validated['name_last'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'company_id' => 1,
            ];

            $role = $validated['role'];

            $infoAttributes = [
                'phone' => $validated['phone'] ?? '',
            ];

            $user = User::on('tenant')->create($attributes ?? []);
            $user->assignRole($role);
            UserInfo::on('tenant')->create([
                'user_id' => $user->id,
                ...$infoAttributes,
            ]);

            $this->tenantService->resetTenant();

            return response()->json(['message' => 'Member added successfully']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  string  $uuid
     * @param  string  $userUuid
     * @return Response|JsonResponse
     */
    public function updateUser(Request $request, string $uuid, string $userUuid): Response|JsonResponse
    {

        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return null;
            }

            $tenant = $this->tenantService->setTenant($company->name);

            if (!$tenant) {
                return null;
            }

            $rules = [
                'name_first' => 'required|string|max:255',
                'name_last' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('tenant.users', 'email')
                        ->ignore($userUuid, 'uuid'),
                ],
                'password' => 'sometimes|string|min:8|confirmed',
                'phone' => 'required|string|max:20',
                'role' => [
                    'required',
                    'string',
                    Rule::exists('tenant.roles', 'name')
                ],
            ];

            $validated = $request->validate($rules);
            
            $user = User::on('tenant')->where('uuid', $userUuid)->first();

            if($user)
            {
                $validatedUserInput = $request->only([
                    'name_first',
                    'name_last',
                    'email',
                    'password',
                ]);
    
                $validatedInfoInput = $request->only([
                    'phone' => $validated['phone'] ?? '',
                ]);
    
                $role = $validated['role'];

                $user->update($validatedUserInput);

                if ($role !== $user->getRoleNames()->first()) {
                    $user->syncRoles($role);
                }

                $userInfo = UserInfo::on('tenant')->where('user_id', $user->id)->first();
                $userInfo->update($validatedInfoInput);

                $this->tenantService->resetTenant();

                return response()->json(['message' => 'Member updated successfully']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @param  string  $userUuid
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(string $uuid, string $userUuid): JsonResponse
    {
        if(Auth::user()->hasRole(UserRole::SUPER_ADMIN->value))
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);
            
            if (!$company) {
                return null;
            }
            
            $tenant = $this->tenantService->setTenant($company->name);
            
            if (!$tenant) {
                return null;
            }
            
            $user = User::on('tenant')->where('uuid', $userUuid)->first();

            if($user)
            {
                $user->delete();

                $this->tenantService->resetTenant();

                return response()->json([
                    'message' => 'Member deleted successfully',
                ]);
            }
        }
    }

    public function wipeData(string $uuid): JsonResponse
    {
        if(Auth::user()->email === 'eric@withdarby.com' || Auth::user()->email === 'imhamzaaslam@gmail.com')
        {
            $company = $this->companyRepository->getByUuidOrFail($uuid);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            try {
                $tenantAdmin = User::role('Admin')->orderBy('id', 'asc')->first();

                if (!$tenantAdmin) {
                    return response()->json(['message' => 'No admin user found'], 404);
                }

                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                // Step 1: Delete or truncate dependent data FIRST
                DB::table('user_infos')->where('user_id', '!=', $tenantAdmin->id)->delete();
                DB::table('chat_messages')->truncate(); // Move up
                DB::table('chats')->truncate(); // If it also references users
                DB::table('model_has_roles')->where('model_id', '!=', $tenantAdmin->id)->delete();

                DB::table('users')->where('id', '!=', $tenantAdmin->id)->delete();
                DB::table('projects')->truncate();
                DB::table('project_lists')->truncate();
                DB::table('project_bucks')->truncate();
                DB::table('project_members')->truncate();
                DB::table('project_services')->truncate();
                DB::table('tasks')->truncate();
                DB::table('task_assignees')->truncate();
                DB::table('calendar_events')->truncate();
                DB::table('calendar_event_guests')->truncate();
                DB::table('folders')->truncate();
                DB::table('files')->truncate();
                DB::table('milestones')->truncate();
                DB::table('notifications')->truncate();
                DB::table('payments')->truncate();
                DB::table('template_lists')->truncate();
                DB::table('template_list_tasks')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');

                return response()->json(['message' => 'Wipe data successful'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Wipe data failed', 'error' => $e->getMessage()], 500);
            }

            return response()->json(['message' => 'Tenant data wiped successfully'], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
