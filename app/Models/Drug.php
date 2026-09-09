<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'code', 'name', 'generic_name', 'type', 'specification', 'manufacturer', 'unit', 'dispensing_unit', 'conversion_rate', 'retail_price', 'purchase_price', 'requires_batch', 'is_enabled'])]
class Drug extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return ['requires_batch' => 'boolean', 'is_enabled' => 'boolean'];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }
}
