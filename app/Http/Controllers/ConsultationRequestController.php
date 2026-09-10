<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequest;
use App\Models\ConsultationRequest;
use Illuminate\Http\RedirectResponse;

class ConsultationRequestController extends Controller
{
    public function store(StoreConsultationRequest $request): RedirectResponse
    {
        ConsultationRequest::query()->create([
            ...$request->safe()->except(['consent', 'website']),
            'source' => 'website',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('consultation_submitted', true);
    }
}
