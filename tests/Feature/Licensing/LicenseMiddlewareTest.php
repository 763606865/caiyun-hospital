<?php

namespace Tests\Feature\Licensing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use OpenSSLAsymmetricKey;
use Tests\TestCase;

class LicenseMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private OpenSSLAsymmetricKey $privateKey;

    protected function setUp(): void
    {
        parent::setUp();
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        self::assertInstanceOf(OpenSSLAsymmetricKey::class, $key);
        $this->privateKey = $key;
        $details = openssl_pkey_get_details($key);
        self::assertIsArray($details);

        config([
            'license.enabled' => true,
            'license.public_key' => $details['key'],
            'license.verify_domain' => true,
            'license.leeway' => 0,
        ]);

        Route::middleware(['web', 'feature:ai'])->get('/test/licensed-ai', fn () => response()->json(['ok' => true]));
        Route::middleware(['web', 'feature:payment'])->get('/test/licensed-payment', fn () => response()->json(['ok' => true]));
    }

    public function test_valid_license_allows_requests_and_granted_features(): void
    {
        config(['license.token' => $this->token()]);

        $this->getJson('/api/cms/categories')->assertOk();
        $this->getJson('/test/licensed-ai')->assertOk()->assertJson(['ok' => true]);
    }

    public function test_expired_license_blocks_requests(): void
    {
        config(['license.token' => $this->token(['expires_at' => now()->subMinute()->toIso8601String()])]);

        $this->getJson('/api/cms/categories')->assertForbidden()->assertJsonPath('message', '项目授权已过期，请联系服务商续费');
    }

    public function test_unlicensed_domain_is_rejected(): void
    {
        config(['license.token' => $this->token(['domains' => ['licensed.example.com']])]);

        $this->getJson('http://other.example.com/api/cms/categories')->assertForbidden();
    }

    public function test_feature_middleware_rejects_missing_entitlement(): void
    {
        config(['license.token' => $this->token(['features' => ['ai']])]);

        $this->getJson('/test/licensed-payment')->assertForbidden()->assertJsonPath('message', '当前套餐未包含支付模块');
    }

    public function test_tampered_license_is_rejected(): void
    {
        $token = $this->token();
        config(['license.token' => 'x'.substr($token, 1)]);

        $this->getJson('/api/cms/categories')->assertForbidden();
    }

    public function test_health_check_is_always_available(): void
    {
        config(['license.token' => null]);

        $this->get('/up')->assertOk();
    }

    /** @param array<string, mixed> $overrides */
    private function token(array $overrides = []): string
    {
        $claims = array_replace([
            'license_id' => 'LIC-TEST-001', 'customer' => '测试客户', 'plan' => 'pro',
            'issued_at' => now()->subDay()->toIso8601String(), 'not_before' => now()->subMinute()->toIso8601String(),
            'expires_at' => now()->addYear()->toIso8601String(), 'features' => ['payment', 'mobile', 'ai'],
            'domains' => ['localhost'],
        ], $overrides);
        $payload = $this->base64Url((string) json_encode($claims, JSON_THROW_ON_ERROR));
        $signed = openssl_sign($payload, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        self::assertTrue($signed);

        return $payload.'.'.$this->base64Url($signature);
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
