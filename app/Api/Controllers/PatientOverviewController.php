<?php

namespace App\Api\Controllers;

use App\Enums\HsAppointmentStatus;
use App\Models\HsAppointment;
use App\Models\HsPatient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 患者端「首页/我的」登录态聚合。
 */
class PatientOverviewController extends Controller
{
    /**
     * 登录用户首页/我的页聚合数据。
     *
     * GET /api/me/overview
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['nullable', 'integer', 'min:1'],
            'campus' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $this->user();
        $patients = HsPatient::query()
            ->where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get();

        $defaultPatient = $patients->firstWhere('is_default', true) ?? $patients->first();

        $patientId = $validated['patient_id'] ?? $defaultPatient?->id;
        if ($patientId !== null && ! $patients->contains('id', (int) $patientId)) {
            $patientId = $defaultPatient?->id;
        }

        $selectedPatient = $patients->firstWhere('id', $patientId);

        $pendingQuery = HsAppointment::query()
            ->where('user_id', $user->id)
            ->where('status', HsAppointmentStatus::Pending)
            ->when($patientId, fn ($query) => $query->where('patient_id', $patientId));

        $todayAppointments = (clone $pendingQuery)
            ->whereDate('appointment_date', now()->toDateString())
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('campus', fn ($q) => $q->where('slug', $campusSlug));
            })
            ->with([
                'patient:id,name,relation',
                'doctor:id,name,slug,title,avatar',
                'department:id,name,slug,location',
                'campus:id,name,slug,address,latitude,longitude',
                'schedule:id,room,visit_type,status',
            ])
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'real_name' => $user->real_name,
                'nick_name' => $user->nick_name,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'is_verified' => filled($user->real_name),
            ],
            'default_patient' => $defaultPatient,
            'selected_patient' => $selectedPatient,
            'patients' => $patients,
            'today_appointments' => $todayAppointments,
            'stats' => [
                'pending_appointments' => (clone $pendingQuery)->count(),
                'patients' => $patients->count(),
                // 报告/缴费需对接 LIS / HIS，本地闭环暂返回 0
                'reports' => 0,
                'pending_payments' => 0,
            ],
        ]);
    }
}
