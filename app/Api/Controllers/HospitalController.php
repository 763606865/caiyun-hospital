<?php

namespace App\Api\Controllers;

use App\Models\HsAppointmentSetting;
use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 医院公开信息接口（院区、科室、医生、排班、预约规则）。
 *
 * 无需登录，供患者端浏览与选号。
 */
class HospitalController extends Controller
{
    /**
     * 已启用院区列表。
     *
     * GET /api/hospital/campuses
     */
    public function campuses(): JsonResponse
    {
        $campuses = HsCampus::query()
            ->enabled()
            ->orderBy('sort')
            ->orderBy('id')
            ->get([
                'id', 'name', 'slug', 'address', 'phone', 'latitude', 'longitude',
                'open_hours', 'sort',
            ]);

        return $this->success($campuses);
    }

    /**
     * 院区详情。
     *
     * GET /api/hospital/campuses/{slug}
     */
    public function campus(string $slug): JsonResponse
    {
        $campus = HsCampus::query()
            ->enabled()
            ->where('slug', $slug)
            ->firstOrFail([
                'id', 'name', 'slug', 'address', 'phone', 'latitude', 'longitude',
                'open_hours', 'sort',
            ]);

        return $this->success($campus);
    }

    /**
     * 科室分页列表。
     *
     * 支持按院区 slug、关键词筛选。
     *
     * GET /api/hospital/departments
     */
    public function departments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campus' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $departments = HsDepartment::query()
            ->enabled()
            ->with(['campus:id,name,slug'])
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->when($validated['q'] ?? null, function ($query, string $keyword): void {
                $query->where(function ($q) use ($keyword): void {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('specialties', 'like', "%{$keyword}%")
                        ->orWhere('summary', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('sort')
            ->orderBy('id')
            ->paginate($validated['per_page'] ?? 20, [
                'id', 'campus_id', 'name', 'slug', 'summary', 'specialties',
                'cover', 'location', 'sort',
            ]);

        return $this->success($departments);
    }

    /**
     * 科室详情（含所属院区与已启用医生）。
     *
     * GET /api/hospital/departments/{slug}
     */
    public function department(string $slug): JsonResponse
    {
        $department = HsDepartment::query()
            ->enabled()
            ->where('slug', $slug)
            ->with([
                'campus:id,name,slug,address,phone',
                'doctors' => fn ($query) => $query->enabled()->orderBy('sort')->orderBy('id'),
            ])
            ->firstOrFail();

        return $this->success($department);
    }

    /**
     * 医生分页列表。
     *
     * 支持按科室、院区 slug、关键词筛选。
     *
     * GET /api/hospital/doctors
     */
    public function doctors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department' => ['nullable', 'string', 'max:255'],
            'campus' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $doctors = HsDoctor::query()
            ->enabled()
            ->with(['departments:id,name,slug,campus_id'])
            ->when($validated['department'] ?? null, function ($query, string $departmentSlug): void {
                $query->whereHas('departments', fn ($q) => $q->where('slug', $departmentSlug)->where('is_enabled', true));
            })
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('departments.campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->when($validated['q'] ?? null, function ($query, string $keyword): void {
                $query->where(function ($q) use ($keyword): void {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('title', 'like', "%{$keyword}%")
                        ->orWhere('specialties', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('sort')
            ->orderBy('id')
            ->paginate($validated['per_page'] ?? 20, [
                'id', 'name', 'slug', 'title', 'specialties', 'summary', 'avatar', 'fee', 'sort',
            ]);

        return $this->success($doctors);
    }

    /**
     * 医生详情（含所属科室）。
     *
     * GET /api/hospital/doctors/{slug}
     */
    public function doctor(string $slug): JsonResponse
    {
        $doctor = HsDoctor::query()
            ->enabled()
            ->where('slug', $slug)
            ->with(['departments' => fn ($query) => $query->enabled()->orderBy('sort')])
            ->firstOrFail();

        return $this->success($doctor);
    }

    /**
     * 可约排班分页列表。
     *
     * 仅返回正常出诊排班，并附带可约号源；默认日期范围为今天至预约规则提前天数。
     *
     * GET /api/hospital/schedules
     */
    public function schedules(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'campus' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $settings = HsAppointmentSetting::current();
        $dateFrom = $validated['date_from'] ?? now()->toDateString();
        $dateTo = $validated['date_to'] ?? now()->addDays(max(0, (int) $settings->advance_days))->toDateString();

        $schedules = HsSchedule::query()
            ->bookable()
            ->whereDate('schedule_date', '>=', $dateFrom)
            ->whereDate('schedule_date', '<=', $dateTo)
            ->with([
                'doctor:id,name,slug,title,avatar,fee',
                'department:id,name,slug',
                'campus:id,name,slug',
                'quotas' => fn ($query) => $query->where('is_enabled', true)->orderBy('sort')->orderBy('start_time'),
            ])
            ->when($validated['doctor'] ?? null, function ($query, string $doctorSlug): void {
                $query->whereHas('doctor', fn ($q) => $q->where('slug', $doctorSlug)->where('is_enabled', true));
            })
            ->when($validated['department'] ?? null, function ($query, string $departmentSlug): void {
                $query->whereHas('department', fn ($q) => $q->where('slug', $departmentSlug)->where('is_enabled', true));
            })
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->orderBy('schedule_date')
            ->orderBy('period')
            ->orderBy('id')
            ->paginate($validated['per_page'] ?? 30);

        return $this->success($schedules);
    }

    /**
     * 排班详情（含医生、科室、院区与可约号源）。
     *
     * GET /api/hospital/schedules/{id}
     */
    public function schedule(int $id): JsonResponse
    {
        $schedule = HsSchedule::query()
            ->bookable()
            ->whereKey($id)
            ->with([
                'doctor:id,name,slug,title,avatar,fee,specialties,summary',
                'department:id,name,slug,location',
                'campus:id,name,slug,address',
                'quotas' => fn ($query) => $query->where('is_enabled', true)->orderBy('sort')->orderBy('start_time'),
            ])
            ->firstOrFail();

        return $this->success($schedule);
    }

    /**
     * 当前预约规则（放号天数、取消时限、爽约限制、须知等）。
     *
     * GET /api/hospital/appointment-settings
     */
    public function appointmentSettings(): JsonResponse
    {
        $settings = HsAppointmentSetting::current();

        return $this->success([
            'registration_enabled' => $settings->registration_enabled,
            'advance_days' => $settings->advance_days,
            'cancel_hours_before' => $settings->cancel_hours_before,
            'no_show_limit' => $settings->no_show_limit,
            'no_show_ban_days' => $settings->no_show_ban_days,
            'notice' => $settings->notice,
        ]);
    }
}
