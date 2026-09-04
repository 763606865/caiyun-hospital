<?php

namespace App\Models;

use App\Enums\HsSchedulePeriod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * 体检可预约场次。
 *
 * @property int $id
 * @property int $package_id
 * @property int $campus_id
 * @property Carbon $slot_date
 * @property HsSchedulePeriod $period
 * @property int $total
 * @property int $remaining
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read HsCheckupPackage $package
 * @property-read HsCampus $campus
 * @property-read Collection<int, HsCheckupOrder> $orders
 *
 * @method static Builder<static> available()
 */
#[Table(name: 'hs_checkup_slots')]
#[Fillable([
    'package_id', 'campus_id', 'slot_date', 'period',
    'total', 'remaining', 'is_enabled',
])]
class HsCheckupSlot extends Model
{
    /** @var array<string, mixed> */
    protected $attributes = [
        'total' => 0,
        'remaining' => 0,
        'is_enabled' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'period' => HsSchedulePeriod::class,
            'is_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<HsCheckupPackage, $this> */
    public function package(): BelongsTo
    {
        return $this->belongsTo(HsCheckupPackage::class, 'package_id');
    }

    /** @return BelongsTo<HsCampus, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(HsCampus::class, 'campus_id');
    }

    /** @return HasMany<HsCheckupOrder, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(HsCheckupOrder::class, 'slot_id');
    }

    /**
     * @param  Builder<HsCheckupSlot>  $query
     * @return Builder<HsCheckupSlot>
     */
    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->where('is_enabled', true)->where('remaining', '>', 0);
    }
}
