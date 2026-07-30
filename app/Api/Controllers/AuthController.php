<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Caiyun\Sms\Contracts\SmsSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Random\RandomException;

class AuthController extends Controller
{
    private const SMS_CODE_TTL_SECONDS = 300;

    /**
     * 发送短信验证码
     *
     * POST /api/auth/sms-code
     *
     * @throws RandomException
     */
    public function sendSmsCode(Request $request, SmsSender $sms): JsonResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
        ]);

        $code = (string) random_int(100000, 999999);

        $sms->send($validated['mobile'], 'verification_code', [
            'code' => $code,
        ]);

        Cache::put(
            $this->smsCodeCacheKey($validated['mobile']),
            $code,
            self::SMS_CODE_TTL_SECONDS,
        );

        return response()->json([
            'message' => '验证码发送成功',
            'expires_in' => self::SMS_CODE_TTL_SECONDS,
        ]);
    }

    /**
     * 短信验证码登录
     *
     * 手机号未注册时自动创建用户，并返回访问令牌。
     *
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'regex:/^1[3-9]\d{9}$/'],
            'code' => ['required', 'string', 'size:6'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ]);

        $cachedCode = Cache::pull($this->smsCodeCacheKey($validated['mobile']));

        if (! is_string($cachedCode) || ! hash_equals($cachedCode, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => ['验证码错误或已过期'],
            ]);
        }

        $user = User::query()->firstOrCreate(
            ['mobile' => $validated['mobile']],
            [
                'name' => '用户'.substr($validated['mobile'], -4),
                'email' => sprintf('%s@mobile.local', $validated['mobile']),
                'password' => Str::password(32),
            ],
        );

        $token = $user->createToken($validated['device_name'] ?? 'api')->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => $this->userData($user),
        ]);
    }

    /**
     * 获取当前登录用户信息
     *
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'user' => $this->userData($user),
        ]);
    }

    private function smsCodeCacheKey(string $mobile): string
    {
        return 'auth:sms-code:'.$mobile;
    }

    /**
     * @return array<string, mixed>
     */
    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'mobile' => $user->mobile,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
