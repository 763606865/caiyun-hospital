<?php

namespace App\Models\Pivot;

use App\Models\HsCheckupItem;
use App\Models\HsCheckupPackage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 体检套餐与检查项目中间表。
 *
 * @property int $id
 * @property int $package_id
 * @property int $item_id
 * @property int $sort
 */
#[Table(name: 'hs_checkup_package_item')]
#[Fillable(['package_id', 'item_id', 'sort'])]
class HsCheckupPackageItem extends Pivot
{
    public $incrementing = true;

    /** @return BelongsTo<HsCheckupPackage, $this> */
    public function package(): BelongsTo
    {
        return $this->belongsTo(HsCheckupPackage::class, 'package_id');
    }

    /** @return BelongsTo<HsCheckupItem, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(HsCheckupItem::class, 'item_id');
    }
}
