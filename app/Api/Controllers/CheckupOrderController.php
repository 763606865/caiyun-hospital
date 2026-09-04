<?php

namespace App\Api\Controllers;

use App\Enums\HsCheckupOrderStatus;
use App\Models\HsCheckupOrder;
use App\Services\Hospital\CheckupBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * 患者端体检预约接口。
 *
 * 需登录；下单/取消逻辑委托 {@see CheckupBookingService}。
 */
class CheckupOrderController extends Controller
{
    public function __construct(
        protected CheckupBookingService $bookingService,
    ) {}

    /**
     * 当前用户的体检预约单分页列表。
     *
     * GET /api/checkup-orders
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::enum(HsCheckupOrderStatus::class)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $orders = HsCheckupOrder::query()
            ->where('user_id', $this->user()->id)
            ->with([
                'patient:id,name,phone,gender',
                'package:id,name,slug,cover,price',
                'campus:id,name,slug,address',
            ])
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 15);

        return $this->success($orders);
    }

    /**
     * 提交体检预约。
     *
     * POST /api/checkup-orders
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'min:1'],
            'slot_id' => ['required', 'integer', 'min:1'],
            'remark' => ['nullable', 'string', 'max:500'],
        ]);

        $order = $this->bookingService->create(
            $this->user(),
            (int) $validated['patient_id'],
            (int) $validated['slot_id'],
            $validated['remark'] ?? null,
        );

        $order->load([
            'patient:id,name,phone,gender',
            'package:id,name,slug,cover,price',
            'campus:id,name,slug,address',
            'slot:id,slot_date,period,remaining',
        ]);

        return $this->success($order, 201);
    }

    /**
     * 体检预约单详情。
     *
     * GET /api/checkup-orders/{orderNo}
     */
    public function show(string $orderNo): JsonResponse
    {
        $order = $this->findOwnedOrder($orderNo);
        $order->load([
            'patient:id,name,phone,gender,id_type,id_number',
            'package:id,name,slug,cover,price,notice,gender_limit',
            'package.items:id,name,slug,category',
            'campus:id,name,slug,address,phone',
            'slot:id,slot_date,period,total,remaining',
        ]);

        return $this->success($order);
    }

    /**
     * 取消体检预约。
     *
     * POST /api/checkup-orders/{orderNo}/cancel
     */
    public function cancel(Request $request, string $orderNo): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $this->findOwnedOrder($orderNo);
        $order = $this->bookingService->cancel(
            $this->user(),
            $order,
            $validated['reason'] ?? null,
        );

        return $this->success($order);
    }

    /**
     * 按单号查找当前用户的体检预约单，不存在则 404。
     */
    protected function findOwnedOrder(string $orderNo): HsCheckupOrder
    {
        return HsCheckupOrder::query()
            ->where('user_id', $this->user()->id)
            ->where('order_no', $orderNo)
            ->firstOrFail();
    }
}
