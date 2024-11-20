<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Base;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class Company extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
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

    public function makeDomainUrl()
    {
        if ($this->id == 1) {
            return null;
        }

        $tenantId = Str::slug($this->name, '_');
        $domain = Domain::where('tenant_id', $tenantId)->first();

        return $domain ? $domain->domain : null;
    }
}
