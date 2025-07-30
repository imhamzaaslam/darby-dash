<?php

namespace App\Models;

use App\Contracts\BaseInterface;
use App\Contracts\PlatformRepositoryInterface;
use App\Enums\Platform as PlatformEnum;
use App\Enums\FileType;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Rawilk\Settings\Models\HasSettings;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Kodeine\Metable\Metable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole;
use App\Notifications\CustomResetPassword;

/**
 * App\Models\User
 *
 * @property string $uuid
 * @property string $name_first
 * @property string $name_last
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $bookkeeping_started_at
 * @property Carbon|null $oss_registered_at
 * @property string $state
 * @property string|null $yuki_access_key
 * @property UserInfo|null $info
 * @property Collection|Credential[] $credentials
 * @property-read string $name
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $remember_token
 * @property-read int|null $credentials_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VatNumber> $vatNumbers
 * @property-read int|null $vat_numbers_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User meta($alias = null)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereBookkeepingStartedAt($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereMeta($key, $value, $alias = null)
 * @method static Builder|User whereNameFirst($value)
 * @method static Builder|User whereNameLast($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereState($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUuid($value)
 * @method static Builder|User whereYukiAccessKey($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static Builder|User whereOssRegisteredAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail, BaseInterface
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use Metable;
    use HasSettings;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name_first',
        'name_last',
        'email',
        'company_id',
        'email_verified_at',
        'password',
        'state',
        'is_2fa',
        'verification_code',
        'verification_expires_at',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    public function isOnline()
    {
        return $this->last_active_at && $this->last_active_at->diffInMinutes(now()) < 5;
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function avatar(): ?MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('type', FileType::AVATAR->value)
            ->latest();
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            $user->uuid = str()->uuid();

            if (auth()->check() && Schema::hasColumn($user->getTable(), 'created_by')) {
                $user->created_by = auth()->id();
            }
        });

        static::updating(function (self $model) {
            if (auth()->check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function (self $model) {
            if (auth()->check() && Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN->value);
    }

    public function emailIsVerified(): ?Carbon
    {
        return $this->email_verified_at;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'model_has_permissions', 'model_id', 'permission_id');
    }

    function scopeFiltered(Builder $query, ?string $name, ?string $email, ?string $roleId): Builder
    {
        $usersTable = (new User())->getTable();

        return $query->when($name, function (Builder $query) use ($name, $usersTable) {
            $names = explode(" ", $name);
            if (count($names) === 2) {
                return $query->where("$usersTable.name_first", 'like', '%' . $names[0] . '%')
                    ->where("$usersTable.name_last", 'like', '%' . $names[1] . '%');
            } else {
                return $query->where("$usersTable.name_first", 'like', '%' . $name . '%')
                    ->orWhere("$usersTable.name_last", 'like', '%' . $name . '%');
            }
        })->when($email, function (Builder $query) use ($email, $usersTable) {
            return $query->where("$usersTable.email", 'like', '%' . $email . '%');
        })->when($roleId, function (Builder $query) use ($roleId) {
            return $query->whereHas('roles', function (Builder $query) use ($roleId) {
                $query->where('id', $roleId);
            });
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        if ($orderBy === 'name') {
            $orderBy = 'name_first';
        }
        return $query->orderBy($orderBy, $order);
    }

    public function generate2FACode()
    {
        $this->verification_code = rand(100000, 999999);
        $this->verification_expires_at = Carbon::now()->addMinutes(15);
        $this->save();
    }

    public function reset2FACode()
    {
        $this->verification_code = null;
        $this->verification_expires_at = null;
        $this->save();
    }

    public function unseenMessagesFromTeamMember($teamMemberId, $projectId)
    {
        $unseenCount = ChatMessage::where('sender_id', $teamMemberId)->where('receiver_id', $this->id)
        ->whereHas('chat', function ($query) use ($projectId)  {
            $query->where('project_id', $projectId);
        })->where('is_seen', false) 
        ->count();

        return $unseenCount;
    }
}
