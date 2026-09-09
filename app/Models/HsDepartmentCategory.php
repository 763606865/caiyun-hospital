<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 科室分类（挂号页左侧分组）。
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $sort
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, HsDepartment> $departments
 *
 * @method static Builder<static> enabled()
 */
#[Table(name: 'hs_department_categories')]
#[Fillable(['name', 'slug', 'sort', 'is_enabled'])]
class HsDepartmentCategory extends Model
{
    use BelongsToOrganization, SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /** @return HasMany<HsDepartment, $this> */
    public function departments(): HasMany
    {
        return $this->hasMany(HsDepartment::class, 'category_id')->orderBy('sort');
    }

    /**
     * @param  Builder<HsDepartmentCategory>  $query
     * @return Builder<HsDepartmentCategory>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
