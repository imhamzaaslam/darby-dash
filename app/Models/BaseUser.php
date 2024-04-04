<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\BaseUser
 *
 * @method static Builder|BaseUser newModelQuery()
 * @method static Builder|BaseUser newQuery()
 * @method static Builder|BaseUser query()
 * @mixin \Eloquent
 */
class BaseUser extends Authenticatable
{
    use HasFactory;
}
