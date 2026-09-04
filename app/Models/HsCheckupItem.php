<?php

namespace App\Models;

use App\Models\Pivot\HsCheckupPackageItem;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 体检检查项目。
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $category
 * @property string|null $summary
 * @property int $sort
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, HsCheckupPackage> $packages
 *
 * @method static Builder<static> enabled()
 */
#[Table(name: 'hs_checkup_items')]
#[Fillable(['name', 'slug', 'category', 'summary', 'sort', 'is_enabled'])]
class HsCheckupItem extends Model
{
    use SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<HsCheckupPackage, $this, HsCheckupPackageItem>
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(HsCheckupPackage::class, (new HsCheckupPackageItem)->getTable(), 'item_id', 'package_id')
            ->using(HsCheckupPackageItem::class)
            ->withPivot('sort')
            ->withTimestamps();
    }

    /**
     * @param  Builder<HsCheckupItem>  $query
     * @return Builder<HsCheckupItem>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
