<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'admin_user_id', 'member_no', 'display_name', 'phone', 'job_title', 'role', 'permissions', 'status', 'joined_at', 'left_at'])]
class OrganizationMember extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return ['permissions' => 'array', 'joined_at' => 'datetime', 'left_at' => 'datetime'];
    }

    /** @return BelongsTo<AdminUser, $this> */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }

    /** @return BelongsToMany<Branch, $this> */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_members')->withPivot('is_primary')->withTimestamps();
    }
}
