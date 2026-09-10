<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'contact', 'organization_name', 'organization_type', 'request_type',
    'requirements', 'status', 'follow_up_notes', 'source', 'ip', 'user_agent',
])]
class ConsultationRequest extends Model
{
    //
}
