<?php

namespace App\Api\Controllers;

use App\Models\HsCheckupPackage;
use App\Models\HsCheckupSetting;
use App\Models\HsCheckupSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 体检公开信息接口（套餐、场次、预约规则）。
 *
 * 无需登录，供患者端浏览与选约。
 */
class CheckupController extends Controller
{
    /**
     * 已上架体检套餐分页列表。
     *
     * GET /api/hospital/checkup-packages
     */
    public function packages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campus' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $packages = HsCheckupPackage::query()
            ->enabled()
            ->with(['campus:id,name,slug', 'items' => fn ($query) => $query->enabled()])
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->when($validated['q'] ?? null, function ($query, string $keyword): void {
                $query->where(function ($q) use ($keyword): void {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('summary', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('sort')
            ->orderBy('id')
            ->paginate($validated['per_page'] ?? 20);

        return $this->success($packages);
    }

    /**
     * 体检套餐详情（含检查项目）。
     *
     * GET /api/hospital/checkup-packages/{slug}
     */
    public function package(string $slug): JsonResponse
    {
        $package = HsCheckupPackage::query()
            ->enabled()
            ->where('slug', $slug)
            ->with([
                'campus:id,name,slug,address,phone',
                'items' => fn ($query) => $query->enabled()->orderBy('hs_checkup_items.sort'),
            ])
            ->firstOrFail();

        return $this->success($package);
    }

    /**
     * 可约体检场次列表。
     *
     * GET /api/hospital/checkup-slots
     */
    public function slots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'package' => ['nullable', 'string', 'max:255'],
            'campus' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $settings = HsCheckupSetting::current();
        $dateFrom = $validated['date_from'] ?? now()->toDateString();
        $dateTo = $validated['date_to'] ?? now()->addDays(max(0, (int) $settings->advance_days))->toDateString();

        $slots = HsCheckupSlot::query()
            ->available()
            ->whereDate('slot_date', '>=', $dateFrom)
            ->whereDate('slot_date', '<=', $dateTo)
            ->with([
                'package:id,name,slug,price,gender_limit,cover',
                'campus:id,name,slug',
            ])
            ->whereHas('package', fn ($q) => $q->where('is_enabled', true))
            ->when($validated['package'] ?? null, function ($query, string $packageSlug): void {
                $query->whereHas('package', fn ($q) => $q->where('slug', $packageSlug)->where('is_enabled', true));
            })
            ->when($validated['campus'] ?? null, function ($query, string $campusSlug): void {
                $query->whereHas('campus', fn ($q) => $q->where('slug', $campusSlug)->where('is_enabled', true));
            })
            ->orderBy('slot_date')
            ->orderBy('period')
            ->orderBy('id')
            ->paginate($validated['per_page'] ?? 30);

        return $this->success($slots);
    }

    /**
     * 当前体检预约规则。
     *
     * GET /api/hospital/checkup-settings
     */
    public function settings(): JsonResponse
    {
        $settings = HsCheckupSetting::current();

        return $this->success([
            'booking_enabled' => $settings->booking_enabled,
            'advance_days' => $settings->advance_days,
            'cancel_hours_before' => $settings->cancel_hours_before,
            'no_show_limit' => $settings->no_show_limit,
            'no_show_ban_days' => $settings->no_show_ban_days,
            'notice' => $settings->notice,
        ]);
    }
}
