<?php

namespace App\Api\Controllers;

use App\Enums\HsAppointmentStatus;
use App\Models\HsAppointment;
use App\Services\Hospital\AppointmentBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * 患者端挂号预约接口。
 *
 * 需登录；下单/取消逻辑委托 {@see AppointmentBookingService}。
 */
class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentBookingService $bookingService,
    ) {}

    /**
     * 当前用户的预约单分页列表。
     *
     * GET /api/appointments
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::enum(HsAppointmentStatus::class)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $appointments = HsAppointment::query()
            ->where('user_id', $this->user()->id)
            ->with([
                'patient:id,name,phone,id_type,id_number',
                'doctor:id,name,slug,title,avatar',
                'department:id,name,slug',
                'campus:id,name,slug,address',
            ])
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('appointment_date')
            ->orderByDesc('id')
            ->paginate($validated['per_page'] ?? 15);

        return $this->success($appointments);
    }

    /**
     * 提交挂号。
     *
     * 校验号源余量、排班状态、放号天数与爽约限制后扣号建单。
     *
     * POST /api/appointments
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'min:1'],
            'quota_id' => ['required', 'integer', 'min:1'],
            'remark' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment = $this->bookingService->create(
            $this->user(),
            (int) $validated['patient_id'],
            (int) $validated['quota_id'],
            $validated['remark'] ?? null,
        );

        $appointment->load([
            'patient:id,name,phone',
            'doctor:id,name,slug,title,avatar',
            'department:id,name,slug',
            'campus:id,name,slug,address',
            'quota:id,start_time,end_time,remaining',
        ]);

        return $this->success($appointment, 201);
    }

    /**
     * 预约单详情。
     *
     * GET /api/appointments/{appointmentNo}
     */
    public function show(string $appointmentNo): JsonResponse
    {
        $appointment = $this->findOwnedAppointment($appointmentNo);
        $appointment->load([
            'patient:id,name,phone,id_type,id_number,relation',
            'doctor:id,name,slug,title,avatar,fee',
            'department:id,name,slug,location',
            'campus:id,name,slug,address,phone',
            'schedule:id,schedule_date,period,room,status',
            'quota:id,start_time,end_time',
        ]);

        return $this->success($appointment);
    }

    /**
     * 取消预约。
     *
     * 仅待就诊可取消；成功后回补号源。
     *
     * POST /api/appointments/{appointmentNo}/cancel
     */
    public function cancel(Request $request, string $appointmentNo): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $appointment = $this->findOwnedAppointment($appointmentNo);
        $appointment = $this->bookingService->cancel(
            $this->user(),
            $appointment,
            $validated['reason'] ?? null,
        );

        return $this->success($appointment);
    }

    /**
     * 按预约单号查找当前用户的预约单，不存在则 404。
     */
    protected function findOwnedAppointment(string $appointmentNo): HsAppointment
    {
        return HsAppointment::query()
            ->where('user_id', $this->user()->id)
            ->where('appointment_no', $appointmentNo)
            ->firstOrFail();
    }
}
