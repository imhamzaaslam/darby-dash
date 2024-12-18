<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use App\Models\User;
use App\Models\Base;
use App\Enums\FileType;
use App\Enums\UserRole;
use App\Enums\Settings;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class Company extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function user()
    {
        return $this->users()->orderBy('created_at', 'asc')->first();
    }

    public static function totalCount(): int
    {
        return self::count();
    }

    function scopeFiltered(Builder $query, ?string $keyword): Builder
    {
        $companyTable = (new Company())->getTable();

        return $query->when($keyword, function ($query, $keyword) use ($companyTable) {
            return $query->where(function ($query) use ($companyTable, $keyword) {
                $query->where($companyTable . '.name', 'like', '%' . $keyword . '%');
            });
        });
    }

    public function logo(): ?MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileType::AVATAR->value)
            ->latest();
    }

    public function favicon(): ?MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileType::DOC->value)
            ->latest();
    }

    public function makeDomainUrl()
    {
        if ($this->id == 1) {
            return null;
        }

        $tenantId = Str::slug($this->name, '_');
        $domain = Domain::where('tenant_id', $tenantId)->first();

        return $domain ? $domain->domain : null;
    }

    public function getClient()
    {
        if(Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $admin = User::role('Admin')->orderBy('id', 'asc')->first();

            return $admin ?? null;
        }

        $tenantService = app(TenantService::class);

        $tenant = $tenantService->setTenant($this->name);

        if (!$tenant) {
            return null;
        }

        $admin = User::on('tenant')->role('Admin')->orderBy('id', 'asc')->first();

        $tenantService->resetTenant();

        return $admin ?? null;
    }

    public function getLogo()
    {
        $tenantService = app(TenantService::class);

        $tenant = $tenantService->setTenant($this->name);

        if (!$tenant) {
            return null;
        }

        $company = Company::on('tenant')->where('name', $this->name)->orderBy('id', 'asc')->first();
        $logo = $company->logo;
        $tenantService->resetTenant();

        return $logo ?? null;
    }

    public function getFavicon()
    {
        $tenantService = app(TenantService::class);

        $tenant = $tenantService->setTenant($this->name);

        if (!$tenant) {
            return null;
        }

        $company = Company::on('tenant')->where('name', $this->name)->orderBy('id', 'asc')->first();
        $favicon = $company->favicon;
        $tenantService->resetTenant();

        return $favicon ?? null;
    }

    public function active()
    {
        $tenantService = app(TenantService::class);

        $tenant = $tenantService->setTenant($this->name);

        if (!$tenant) {
            return null;
        }

        $company = Company::on('tenant')->where('name', $this->name)->orderBy('id', 'asc')->first();
        $status = $company->status;
        $tenantService->resetTenant();
        return $status ? 1 : 0;
    }

    public function getGeneralSetting()
    {
        if(Auth::user()->hasRole(UserRole::ADMIN->value))
        {
            $settings = Settings_meta::where('setting_id', Settings::GENERAL->value)->pluck('value', 'key');

            return $settings ?? [];
        }

        $tenantService = app(TenantService::class);

        $tenant = $tenantService->setTenant($this->name);

        if (!$tenant) {
            return null;
        }

        $settings = Settings_meta::on('tenant')->where('setting_id', Settings::GENERAL->value)->pluck('value', 'key');
        $tenantService->resetTenant();

        return $settings ?? [];
    }
}
