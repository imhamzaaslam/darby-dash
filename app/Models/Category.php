<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $uuid
 * @property int|null $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $type
 * @property string|null $name
 * @property string|null $description
 * @property-read Collection<int, Country> $countries
 * @property-read int|null $countries_count
 * @property-read Collection<int, Product> $products
 * @property-read int|null $products_count
 * @property-read Category|null $rootCategory
 * @property-read Collection<int, Category> $subCategories
 * @property-read int|null $sub_categories_count
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCategoryId($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDeletedAt($value)
 * @method static Builder|Category whereDescription($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereType($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category whereUuid($value)
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Category extends Base
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'category_id',
        'type',
        'name',
        'description',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class)->withPivot('rate');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'category_id', 'id');
    }

    public function rootCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
