<?php

namespace App\Http\Middleware;

use App\Enums\ClientPlatform;
use App\Enums\ClientType;
use App\Models\ApiRequestLog;
use App\Models\User;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * 校验并记录 API 客户端、版本、设备和请求链路信息。
 */
class RecordClientContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);
        $requestId = $request->header('X-Request-ID') ?: (string) Str::uuid7();
        $request->attributes->set('request_id', $requestId);
        Log::withContext(['request_id' => $requestId]);

        $headers = $this->headers($request);
        $validator = Validator::make($headers, [
            'client_type' => ['required', new Enum(ClientType::class)],
            'app_version' => ['required', 'string', 'max:50'],
            'app_build' => ['required', 'string', 'max:50'],
            'platform' => ['required', new Enum(ClientPlatform::class)],
            'os_version' => ['required', 'string', 'max:50'],
            'device_id' => ['required', 'string', 'max:191'],
            'channel' => ['required', 'string', 'max:50'],
            'request_id' => ['required', 'uuid'],
        ], [], [
            'client_type' => 'X-Client-Type',
            'app_version' => 'X-App-Version',
            'app_build' => 'X-App-Build',
            'platform' => 'X-Platform',
            'os_version' => 'X-OS-Version',
            'device_id' => 'X-Device-ID',
            'channel' => 'X-Channel',
            'request_id' => 'X-Request-ID',
        ]);

        if ($validator->fails()) {
            $response = ApiResponse::error(
                '客户端信息请求头校验失败',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $validator->errors()->toArray(),
            );
            $validated = [];
        } else {
            $validated = $validator->validated();
            $request->attributes->set('client_context', $validated);
            $response = $next($request);
        }

        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('Access-Control-Expose-Headers', 'X-Request-ID');

        $this->recordRequestLog($request, $response, $validated, $startedAt);

        return $response;
    }

    /**
     * @return array<string, string|null>
     */
    private function headers(Request $request): array
    {
        return [
            'client_type' => $request->header('X-Client-Type'),
            'app_version' => $request->header('X-App-Version'),
            'app_build' => $request->header('X-App-Build'),
            'platform' => $request->header('X-Platform'),
            'os_version' => $request->header('X-OS-Version'),
            'device_id' => $request->header('X-Device-ID'),
            'channel' => $request->header('X-Channel'),
            'request_id' => $request->attributes->get('request_id'),
        ];
    }

    /**
     * 按配置写入请求日志，不查询或更新设备表。
     *
     * @param  array<string, mixed>  $headers
     */
    private function recordRequestLog(
        Request $request,
        Response $response,
        array $headers,
        int $startedAt,
    ): void {
        if (! config('client.request_log_enabled') || ! isset($headers['request_id'])) {
            return;
        }

        try {
            /** @var User|null $user */
            $user = $request->user();
            $now = now();

            ApiRequestLog::query()->create([
                'request_id' => $headers['request_id'],
                'user_id' => $user?->id,
                'user_device_id' => null,
                'method' => $request->method(),
                'path' => $request->path(),
                'route_name' => $request->route()?->getName(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => (int) ((hrtime(true) - $startedAt) / 1_000_000),
                'ip' => $request->ip(),
                'requested_at' => $now,
            ]);
        } catch (Throwable $exception) {
            Log::warning('记录 API 客户端信息失败', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
