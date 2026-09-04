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
     * 支持就诊人筛选与就诊页 Tab：pending/ticket/waiting/completed。
     *
     * GET /api/appointments
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::enum(HsAppointmentStatus::class)],
            'patient_id' => ['nullable', 'integer', 'min:1'],
            'tab' => ['nullable', Rule::in(['pending', 'ticket', 'waiting', 'completed', 'payment'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        // 待缴费依赖 HIS/支付，本地闭环暂无空列表
        if (($validated['tab'] ?? null) === 'payment') {
            return $this->success([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => $validated['per_page'] ?? 15,
                'total' => 0,
            ]);
        }

        $appointments = HsAppointment::query()
            ->where('user_id', $this->user()->id)
            ->with([
                'patient:id,name,phone,id_type,id_number,relation',
                'doctor:id,name,slug,title,avatar',
                'department:id,name,slug,location',
                'campus:id,name,slug,address,latitude,longitude',
                'schedule:id,room,visit_type,status',
            ])
            ->when($validated['patient_id'] ?? null, fn ($query, $patientId) => $query->where('patient_id', $patientId))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['tab'] ?? null, function ($query, string $tab): void {
                match ($tab) {
                    'pending' => $query->where('status', HsAppointmentStatus::Pending),
                    'ticket' => $query->where('status', HsAppointmentStatus::Pending)->whereNull('checked_in_at'),
                    'waiting' => $query->where('status', HsAppointmentStatus::Pending)->whereNotNull('checked_in_at'),
                    'completed' => $query->where('status', HsAppointmentStatus::Completed),
                    default => null,
                };
            })
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
            'department:id,name,slug,location',
            'campus:id,name,slug,address',
            'quota:id,start_time,end_time,remaining',
            'schedule:id,room,visit_type',
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
            'campus:id,name,slug,address,phone,latitude,longitude',
            'schedule:id,schedule_date,period,room,status,visit_type',
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
