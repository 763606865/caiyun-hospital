<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 出诊周模板号源时段。
 *
 * @property int $id
 * @property int $template_id
 * @property string $start_time
 * @property string $end_time
 * @property int $total
 * @property int $sort
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read HsScheduleTemplate $template
 */
#[Table(name: 'hs_schedule_template_slots')]
#[Fillable(['template_id', 'start_time', 'end_time', 'total', 'sort', 'is_enabled'])]
class HsScheduleTemplateSlot extends Model
{
    /** @var array<string, mixed> */
    protected $attributes = [
        'total' => 0,
        'sort' => 0,
        'is_enabled' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<HsScheduleTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(HsScheduleTemplate::class, 'template_id');
    }
}
