<?php

namespace App\Api\Controllers;

use App\Enums\HsScheduleStatus;
use App\Enums\HsVisitType;
use App\Models\Content;
use App\Models\HsAppointmentSetting;
use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDepartmentCategory;
use App\Models\HsDoctor;
use App\Models\HsQuota;
use App\Models\HsSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * 医院公开信息接口（院区、科室、医生、排班、预约规则、首页聚合）。
 *
 * 无需登录，供患者端浏览与选号。
 */
class HospitalController extends Controller
{
    /**
     * 首页公开聚合数据。
     *
     * 含院区、常挂科室、最新停诊/公告摘要。登录后的「今日待就诊」见 GET /api/me/overview。
     *
     * GET /api/hospital/home
     */
    public function home(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campus' => ['nullable', 'string', 'max:255'],
        ]);

        $campusSlug = $validated['campus'] ?? null;

        $campuses = HsCampus::query()
            ->enabled()
            ->orderBy('sort')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'address', 'phone', 'latitude', 'longitude', 'open_hours', 'sort']);

        $featuredDepartments = HsDepartment::query()
            ->enabled()
            ->featured()
            ->with(['campus:id,name,slug', 'category:id,name,slug'])
            ->when($campusSlug, fn ($query) => $query->whereHas(
                'campus',
                fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true)
            ))
            ->orderBy('sort')
            ->orderBy('id')
            ->limit(20)
            ->get(['id', 'campus_id', 'category_id', 'name', 'slug', 'location', 'sort', 'is_featured']);

        $notices = Content::query()
            ->published()
            ->with(['categories:id,name,slug'])
            ->whereHas('categories', fn ($q) => $q->whereIn('slug', ['suspensions', 'notices']))
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'summary', 'published_at', 'is_pinned']);

        $latestSuspension = $notices->first(
            fn (Content $content) => $content->categories->contains(fn ($category) => $category->slug === 'suspensions')
        );

        return $this->success([
            'campuses' => $campuses,
            'featured_departments' => $featuredDepartments,
            'notices' => $notices,
            'latest_suspension' => $latestSuspension,
        ]);
    }

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
     * 科室分类列表（挂号页左侧）。
     *
     * GET /api/hospital/department-categories
     */
    public function departmentCategories(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campus' => ['nullable', 'string', 'max:255'],
        ]);

        $campusSlug = $validated['campus'] ?? null;

        $categories = HsDepartmentCategory::query()
            ->enabled()
            ->withCount([
                'departments as department_count' => function ($query) use ($campusSlug): void {
                    $query->enabled()
                        ->when($campusSlug, fn ($q) => $q->whereHas(
                            'campus',
                            fn ($campus) => $campus->where('slug', $campusSlug)->where('is_enabled', true)
                        ));
                },
            ])
            ->orderBy('sort')
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'sort'])
            ->map(fn (HsDepartmentCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'department_count' => (int) $category->department_count,
            ]);

        return $this->success($categories);
    }

    /**
     * 科室分页列表。
     *
     * 支持按院区、分类、常挂、关键词筛选；可附带今日余号。
     *
     * GET /api/hospital/departments
     */
    public function departments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campus' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:50'],
            'featured' => ['nullable', 'boolean'],
            'q' => ['nullable', 'string', 'max:100'],
            'with_today_remaining' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $departments = HsDepartment::query()
            ->enabled()
            ->with(['campus:id,name,slug', 'category:id,name,slug'])
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->when($validated['category'] ?? null, function ($query, string $categorySlug): void {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug)->where('is_enabled', true));
            })
            ->when(array_key_exists('featured', $validated), fn ($query) => $query->where('is_featured', (bool) $validated['featured']))
            ->when($validated['q'] ?? null, function ($query, string $keyword): void {
                $query->where(function ($q) use ($keyword): void {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('specialties', 'like', "%{$keyword}%")
                        ->orWhere('summary', 'like', "%{$keyword}%")
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->orderBy('sort')
            ->orderBy('id')
            ->paginate($validated['per_page'] ?? 20, [
                'id', 'campus_id', 'category_id', 'name', 'slug', 'summary', 'specialties',
                'cover', 'location', 'sort', 'is_featured',
            ]);

        if ($validated['with_today_remaining'] ?? false) {
            $remainingMap = $this->todayRemainingByDepartment(
                $departments->getCollection()->pluck('id')->all(),
                $validated['campus'] ?? null,
            );

            $departments->setCollection(
                $departments->getCollection()->map(function (HsDepartment $department) use ($remainingMap) {
                    $remaining = $remainingMap[$department->id] ?? null;
                    $department->setAttribute('today_remaining', $remaining);
                    $department->setAttribute(
                        'today_status',
                        $remaining === null ? 'none' : ($remaining > 0 ? 'available' : 'full'),
                    );

                    return $department;
                })
            );
        }

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
                'category:id,name,slug',
                'doctors' => fn ($query) => $query->enabled()->orderBy('sort')->orderBy('id'),
            ])
            ->firstOrFail();

        return $this->success($department);
    }

    /**
     * 医生分页列表。
     *
     * 支持按科室、院区、号别、关键词筛选；可附带今日排班摘要。
     *
     * GET /api/hospital/doctors
     */
    public function doctors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department' => ['nullable', 'string', 'max:255'],
            'campus' => ['nullable', 'string', 'max:255'],
            'visit_type' => ['nullable', Rule::enum(HsVisitType::class)],
            'q' => ['nullable', 'string', 'max:100'],
            'with_today_schedule' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $today = now()->toDateString();
        $withToday = (bool) ($validated['with_today_schedule'] ?? false);

        $doctors = HsDoctor::query()
            ->enabled()
            ->with(['departments:id,name,slug,campus_id,location'])
            ->when($withToday, function ($query) use ($today, $validated): void {
                $query->with(['schedules' => function ($q) use ($today, $validated): void {
                    $q->whereDate('schedule_date', $today)
                        ->with(['quotas' => fn ($quota) => $quota->where('is_enabled', true)])
                        ->when($validated['department'] ?? null, function ($scheduleQuery, string $departmentSlug): void {
                            $scheduleQuery->whereHas('department', fn ($d) => $d->where('slug', $departmentSlug));
                        })
                        ->when($validated['campus'] ?? null, function ($scheduleQuery, string $campusSlug): void {
                            $scheduleQuery->whereHas('campus', fn ($c) => $c->where('slug', $campusSlug));
                        })
                        ->when($validated['visit_type'] ?? null, fn ($scheduleQuery, $visitType) => $scheduleQuery->where('visit_type', $visitType))
                        ->orderBy('period');
                }]);
            })
            ->when($validated['department'] ?? null, function ($query, string $departmentSlug): void {
                $query->whereHas('departments', fn ($q) => $q->where('slug', $departmentSlug)->where('is_enabled', true));
            })
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('departments.campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->when($validated['visit_type'] ?? null, function ($query, $visitType) use ($today): void {
                $query->whereHas('schedules', fn ($q) => $q
                    ->whereDate('schedule_date', $today)
                    ->where('visit_type', $visitType));
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

        if ($withToday) {
            $doctors->setCollection(
                $doctors->getCollection()->map(function (HsDoctor $doctor) {
                    $schedules = $doctor->schedules ?? collect();
                    $remaining = $schedules->sum(
                        fn (HsSchedule $schedule) => $schedule->quotas->sum('remaining')
                    );
                    $suspended = $schedules->contains(
                        fn (HsSchedule $schedule) => $schedule->status === HsScheduleStatus::Suspended
                    );
                    $hasNormal = $schedules->contains(
                        fn (HsSchedule $schedule) => $schedule->status === HsScheduleStatus::Normal
                    );

                    $doctor->setAttribute('today_remaining', (int) $remaining);
                    $doctor->setAttribute('today_status', match (true) {
                        $schedules->isEmpty() => 'none',
                        $suspended && ! $hasNormal => 'suspended',
                        $remaining > 0 => 'available',
                        default => 'full',
                    });
                    $doctor->setAttribute(
                        'today_time_range',
                        $this->formatTodayTimeRange($schedules),
                    );

                    return $doctor;
                })
            );
        }

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
            'visit_type' => ['nullable', Rule::enum(HsVisitType::class)],
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
                'department:id,name,slug,location',
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
            ->when($validated['visit_type'] ?? null, fn ($query, $visitType) => $query->where('visit_type', $visitType))
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

    /**
     * @param  list<int>  $departmentIds
     * @return array<int, int>
     */
    protected function todayRemainingByDepartment(array $departmentIds, ?string $campusSlug = null): array
    {
        if ($departmentIds === []) {
            return [];
        }

        $today = now()->toDateString();

        return HsQuota::query()
            ->where('hs_quotas.is_enabled', true)
            ->whereIn('hs_schedules.department_id', $departmentIds)
            ->whereDate('hs_schedules.schedule_date', $today)
            ->where('hs_schedules.status', HsScheduleStatus::Normal)
            ->join('hs_schedules', 'hs_schedules.id', '=', 'hs_quotas.schedule_id')
            ->when($campusSlug, function ($query) use ($campusSlug): void {
                $query->join('hs_campuses', 'hs_campuses.id', '=', 'hs_schedules.campus_id')
                    ->where('hs_campuses.slug', $campusSlug)
                    ->where('hs_campuses.is_enabled', true);
            })
            ->groupBy('hs_schedules.department_id')
            ->selectRaw('hs_schedules.department_id as department_id, sum(hs_quotas.remaining) as today_remaining')
            ->pluck('today_remaining', 'department_id')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    /**
     * @param  Collection<int, HsSchedule>  $schedules
     */
    protected function formatTodayTimeRange(Collection $schedules): ?string
    {
        $quotas = $schedules
            ->filter(fn (HsSchedule $schedule) => $schedule->status === HsScheduleStatus::Normal)
            ->flatMap(fn (HsSchedule $schedule) => $schedule->quotas);

        if ($quotas->isEmpty()) {
            return null;
        }

        $starts = $quotas->pluck('start_time')->filter()->sort()->values();
        $ends = $quotas->pluck('end_time')->filter()->sort()->values();

        if ($starts->isEmpty() || $ends->isEmpty()) {
            return null;
        }

        return substr((string) $starts->first(), 0, 5).'-'.substr((string) $ends->last(), 0, 5);
    }
}
