<?php

namespace App\Models;

use App\Enums\HsCheckupGenderLimit;
use App\Models\Pivot\HsCheckupPackageItem;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 体检套餐。
 *
 * @property int $id
 * @property int $campus_id
 * @property string $name
 * @property string $slug
 * @property string|null $summary
 * @property string|null $body
 * @property string|null $cover
 * @property string $price
 * @property HsCheckupGenderLimit $gender_limit
 * @property int|null $duration_minutes
 * @property string|null $notice
 * @property int $sort
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read HsCampus $campus
 * @property-read Collection<int, HsCheckupItem> $items
 * @property-read Collection<int, HsCheckupSlot> $slots
 * @property-read Collection<int, HsCheckupOrder> $orders
 *
 * @method static Builder<static> enabled()
 */
#[Table(name: 'hs_checkup_packages')]
#[Fillable([
    'campus_id', 'name', 'slug', 'summary', 'body', 'cover', 'price',
    'gender_limit', 'duration_minutes', 'notice', 'sort', 'is_enabled',
])]
class HsCheckupPackage extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'gender_limit' => HsCheckupGenderLimit::All->value,
        'price' => 0,
        'is_enabled' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'gender_limit' => HsCheckupGenderLimit::class,
            'is_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<HsCampus, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(HsCampus::class, 'campus_id');
    }

    /**
     * @return BelongsToMany<HsCheckupItem, $this, HsCheckupPackageItem>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(HsCheckupItem::class, (new HsCheckupPackageItem)->getTable(), 'package_id', 'item_id')
            ->using(HsCheckupPackageItem::class)
            ->withPivot('sort')
            ->withTimestamps()
            ->orderByPivot('sort');
    }

    /** @return HasMany<HsCheckupSlot, $this> */
    public function slots(): HasMany
    {
        return $this->hasMany(HsCheckupSlot::class, 'package_id');
    }

    /** @return HasMany<HsCheckupOrder, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(HsCheckupOrder::class, 'package_id');
    }

    /**
     * @param  Builder<HsCheckupPackage>  $query
     * @return Builder<HsCheckupPackage>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
