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
        'email_verified_at',
        'api_token',
        'password',
        'state',
        'yuki_access_key',
        'bookkeeping_started_at',
        'oss_registered_at',
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
        'oss_registered_at' => 'datetime',
        'bookkeeping_started_at' => 'datetime',
    ];

    /**
     * The events that should be logged.
     *
     * @var array<string, string>
     */
    protected static $recordEvents = ['created'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['uuid', 'name_first', 'name_last']);
    }

    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    /**
     * This is still supported for backward compatibility.
     * It is favoured to retrieve this relation via the Shop relation.
     * This method might be deprecated in the future.
     *
     * @return HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * This is still supported for backward compatibility.
     * It is favoured to retrieve this relation via the Shop relation.
     * This method might be deprecated in the future.
     *
     * @return HasMany
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * This is still supported for backward compatibility.
     * It is favoured to retrieve this relation via the Shop relation.
     * This method might be deprecated in the future.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function vatNumbers(): HasMany
    {
        return $this->hasMany(VatNumber::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
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
            $user->password = Hash::make(str()->random(10));
        });
    }

    public function getNameAttribute(): string
    {
        return "{$this->name_first} {$this->name_last}";
    }

    public function isActive(): bool
    {
        return $this->state === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function emailIsVerified(): ?Carbon
    {
        return $this->email_verified_at;
    }

    public function getStateForCredential(Platform $platform): string
    {
        return $this->credentials
            ->where('user_id', $this->id)
            ->where('platform_id', $platform->id)
            ->first()->state ?? 'inactive';
    }

    public function getActiveCredentials(): Collection
    {
        return $this->credentials()
            ->where('state', 'active')
            ->get();
    }

    public function getCredentialsForPlatform(PlatformEnum $platform, bool $throw = false): ?Credential
    {
        $platform = app(PlatformRepositoryInterface::class)->getFirstBy('client', $platform->value);

        if (!$throw) {
            return $this->credentials->where('platform_id', $platform->id)->first();
        }

        return $this->credentials->where('platform_id', $platform->id)->firstOrFail();
    }

    function scopeFiltered(Builder $query, ?string $keyword): Builder
    {
        $usersTable = (new User())->getTable();
        $userInfosTable = (new UserInfo())->getTable();

        return $query->when($keyword, function(Builder $query) use ($keyword, $usersTable, $userInfosTable) {
            return $query->where("$usersTable.id", 'like', '%' . $keyword . '%')
                ->orWhere("$usersTable.name_first", 'like', '%' . $keyword . '%')
                ->orWhere("$usersTable.name_last", 'like', '%' . $keyword . '%')
                ->orWhere("$usersTable.email", 'like', '%' . $keyword . '%')
                ->orWhere("$usersTable.state", 'like', '%' . $keyword . '%')
                ->orWhereHas('info', function (Builder $query) use ($keyword, $userInfosTable) {
                    $query->where("$userInfosTable.company", 'like', '%' . $keyword . '%');
                    $query->orWhere("$userInfosTable.coc_number", 'like', '%' . $keyword . '%');
                })
                ->orWhereHas('roles', function (Builder $query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                });
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
