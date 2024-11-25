<?php

namespace App\Http\Controllers\Api\Admin;

use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CompanyRepositoryInterface;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\User;
use Carbon\Carbon;
use Exception;
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
use App\Enums\UserRole;

class CompanyController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserInfoRepositoryInterface $userInfoRepository,
        protected CompanyRepositoryInterface $companyRepository,
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
}
