<?php

namespace App\Api\Controllers;

use App\Enums\HsPatientIdType;
use App\Enums\HsPatientRelation;
use App\Enums\UserGender;
use App\Models\HsPatient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * 就诊人管理接口。
 *
 * 需登录；仅可操作当前用户名下的就诊人。
 */
class PatientController extends Controller
{
    /**
     * 当前用户的就诊人列表。
     *
     * 默认就诊人靠前。
     *
     * GET /api/patients
     */
    public function index(): JsonResponse
    {
        $patients = HsPatient::query()
            ->where('user_id', $this->user()->id)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get();

        return $this->success($patients);
    }

    /**
     * 新增就诊人。
     *
     * 首个就诊人自动设为默认；显式指定默认时会取消其他默认。
     *
     * POST /api/patients
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePatient($request);
        $user = $this->user();

        if (! empty($validated['is_default'])) {
            HsPatient::query()->where('user_id', $user->id)->update(['is_default' => false]);
        } elseif (! HsPatient::query()->where('user_id', $user->id)->exists()) {
            $validated['is_default'] = true;
        }

        $patient = HsPatient::query()->create([
            ...$validated,
            'user_id' => $user->id,
        ]);

        return $this->success($patient, 201);
    }

    /**
     * 就诊人详情。
     *
     * GET /api/patients/{id}
     */
    public function show(int $id): JsonResponse
    {
        $patient = $this->findOwnedPatient($id);

        return $this->success($patient);
    }

    /**
     * 更新就诊人。
     *
     * PATCH /api/patients/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $patient = $this->findOwnedPatient($id);
        $validated = $this->validatePatient($request, $patient);

        if (! empty($validated['is_default'])) {
            HsPatient::query()
                ->where('user_id', $this->user()->id)
                ->whereKeyNot($patient->id)
                ->update(['is_default' => false]);
        }

        $patient->fill($validated)->save();

        return $this->success($patient->refresh());
    }

    /**
     * 删除就诊人（软删除）。
     *
     * DELETE /api/patients/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $patient = $this->findOwnedPatient($id);
        $patient->delete();

        return $this->success([
            'message' => '就诊人已删除',
        ]);
    }

    /**
     * 校验就诊人字段。
     *
     * 同一用户下「证件类型 + 证件号」唯一（忽略已软删记录）。
     *
     * @return array<string, mixed>
     */
    protected function validatePatient(Request $request, ?HsPatient $patient = null): array
    {
        $userId = $this->user()->id;

        return $request->validate([
            'name' => [$patient ? 'sometimes' : 'required', 'string', 'max:255'],
            'id_type' => [$patient ? 'sometimes' : 'nullable', Rule::enum(HsPatientIdType::class)],
            'id_number' => [
                $patient ? 'sometimes' : 'required',
                'string',
                'max:64',
                Rule::unique('hs_patients', 'id_number')
                    ->where(fn ($query) => $query
                        ->where('user_id', $userId)
                        ->where('id_type', $request->input('id_type', $patient?->id_type?->value ?? HsPatientIdType::IdCard->value))
                        ->whereNull('deleted_at'))
                    ->ignore($patient?->id),
            ],
            'phone' => [$patient ? 'sometimes' : 'required', 'string', 'max:30'],
            'gender' => ['nullable', Rule::enum(UserGender::class)],
            'birthday' => ['nullable', 'date'],
            'relation' => [$patient ? 'sometimes' : 'nullable', Rule::enum(HsPatientRelation::class)],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * 查找当前用户名下的就诊人，不存在则 404。
     */
    protected function findOwnedPatient(int $id): HsPatient
    {
        return HsPatient::query()
            ->where('user_id', $this->user()->id)
            ->whereKey($id)
            ->firstOrFail();
    }
}
