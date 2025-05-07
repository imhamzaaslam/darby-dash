<?php

namespace App\Models;

use Database\Factories\UserInfoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\UserInfo
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $avatar
 * @property string|null $company
 * @property string|null $coc_number
 * @property string|null $phone
 * @property string|null $website
 * @property string|null $country
 * @property string|null $communication_language
 * @property-read string $avatar_url
 * @property-read mixed|null $communication
 * @property-read User $user
 * @method static UserInfoFactory factory($count = null, $state = [])
 * @method static Builder|UserInfo newModelQuery()
 * @method static Builder|UserInfo newQuery()
 * @method static Builder|UserInfo onlyTrashed()
 * @method static Builder|UserInfo query()
 * @method static Builder|UserInfo whereAvatar($value)
 * @method static Builder|UserInfo whereCocNumber($value)
 * @method static Builder|UserInfo whereCommunicationLanguage($value)
 * @method static Builder|UserInfo whereCompany($value)
 * @method static Builder|UserInfo whereCountry($value)
 * @method static Builder|UserInfo whereCreatedAt($value)
 * @method static Builder|UserInfo whereDeletedAt($value)
 * @method static Builder|UserInfo whereId($value)
 * @method static Builder|UserInfo wherePhone($value)
 * @method static Builder|UserInfo whereUpdatedAt($value)
 * @method static Builder|UserInfo whereUserId($value)
 * @method static Builder|UserInfo whereWebsite($value)
 * @method static Builder|UserInfo withTrashed()
 * @method static Builder|UserInfo withoutTrashed()
 * @mixin \Eloquent
 */
class UserInfo extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'address',
        'city',
        'state',
        'american_state',
        'zip',
    ];

    protected $hidden = ['date_deleted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getAvatarUrlAttribute(): string
    {
        // if file avatar exist in storage folder
        $avatar = public_path(Storage::url($this->avatar));
        if (is_file($avatar) && file_exists($avatar)) {
            // get avatar url from storage
            return Storage::url($this->avatar);
        }

        // check if the avatar is an external url, eg. image from google
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // no avatar, return blank avatar
//        return asset(theme()->getMediaUrlPath().'avatars/blank.png');
        return '';
    }

    /**
     * Deserialize values by default
     *
     * @param $value
     *
     * @return mixed|null
     */
    public function getCommunicationAttribute($value): mixed
    {
        // test to un-serialize value and return as array
        $data = @unserialize($value);
        if ($data !== false) {
            return $data;
        } else {
            return null;
        }
    }
}
