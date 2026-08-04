<?php

namespace Tests\Feature\Licensing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use OpenSSLAsymmetricKey;
use Tests\TestCase;

class RemoteLicenseTest extends TestCase
{
    use RefreshDatabase;

    private OpenSSLAsymmetricKey $privateKey;

    private string $receiptPath;

    protected function setUp(): void
    {
        parent::setUp();
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        self::assertInstanceOf(OpenSSLAsymmetricKey::class, $key);
        $this->privateKey = $key;
        $details = openssl_pkey_get_details($key);
        self::assertIsArray($details);
        $this->receiptPath = sys_get_temp_dir().'/cms-license-receipt-'.bin2hex(random_bytes(8)).'.json';

        config([
            'license.enabled' => true, 'license.token' => $this->licenseToken(), 'license.public_key' => $details['key'],
            'license.verify_domain' => true, 'license.leeway' => 0,
            'license.remote.enabled' => true, 'license.remote.endpoint' => 'https://license.example.test/api/v1/leases',
            'license.remote.instance_id' => 'INSTANCE-001', 'license.remote.receipt_path' => $this->receiptPath,
            'license.remote.retries' => 0, 'license.remote.check_interval_minutes' => 1, 'license.remote.offline_grace_hours' => 72,
        ]);
        Route::middleware(['web', 'feature:payment'])->get('/test/remote-payment', fn () => response()->json(['ok' => true]));
    }

    protected function tearDown(): void
    {
        if (is_file($this->receiptPath)) {
            unlink($this->receiptPath);
        }
        parent::tearDown();
    }

    public function test_remote_server_issues_and_client_caches_a_signed_lease(): void
    {
        Http::fake(['*' => Http::response(['lease' => $this->leaseToken()], 200)]);

        $this->getJson('/api/cms/categories')->assertOk();
        $this->getJson('/api/cms/categories')->assertOk();

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['license_id'] === 'LIC-REMOTE-001'
            && $request['instance_id'] === 'INSTANCE-001' && $request['domain'] === 'localhost');
    }

    public function test_valid_cached_lease_allows_offline_grace_period(): void
    {
        Http::fakeSequence()
            ->push(['lease' => $this->leaseToken()], 200)
            ->push([], 503);
        $this->getJson('/api/cms/categories')->assertOk();

        $this->forceRemoteCheckDue();

        $this->getJson('/api/cms/categories')->assertOk();
        Http::assertSentCount(2);
    }

    public function test_revocation_is_not_hidden_by_cached_lease(): void
    {
        Http::fakeSequence()
            ->push(['lease' => $this->leaseToken()], 200)
            ->push(['reason' => 'revoked', 'message' => '授权已撤销'], 403);
        $this->getJson('/api/cms/categories')->assertOk();

        $this->forceRemoteCheckDue();

        $response = $this->getJson('/api/cms/categories');
        Http::assertSentCount(2);
        $response->assertForbidden()->assertJsonPath('message', '授权已撤销');
    }

    public function test_remote_lease_can_reduce_feature_entitlements(): void
    {
        Http::fake(['*' => Http::response(['lease' => $this->leaseToken(['features' => ['ai']])], 200)]);

        $this->getJson('/test/remote-payment')->assertForbidden()->assertJsonPath('message', '当前套餐未包含支付模块');
    }

    public function test_first_activation_fails_when_remote_server_is_unavailable(): void
    {
        Http::fake(['*' => Http::response([], 503)]);

        $this->getJson('/api/cms/categories')->assertForbidden()->assertJsonPath('message', '远程授权服务暂时不可用');
    }

    /** @param array<string, mixed> $overrides */
    private function licenseToken(array $overrides = []): string
    {
        return $this->signedToken(array_replace([
            'license_id' => 'LIC-REMOTE-001', 'customer' => '远程授权客户', 'plan' => 'pro',
            'issued_at' => now()->subDay()->toIso8601String(), 'not_before' => now()->subMinute()->toIso8601String(),
            'expires_at' => now()->addYear()->toIso8601String(), 'features' => ['payment', 'mobile', 'ai'], 'domains' => ['localhost'],
        ], $overrides));
    }

    /** @param array<string, mixed> $overrides */
    private function leaseToken(array $overrides = []): string
    {
        return $this->signedToken(array_replace([
            'license_id' => 'LIC-REMOTE-001', 'instance_id' => 'INSTANCE-001', 'domain' => 'localhost',
            'issued_at' => now()->toIso8601String(), 'expires_at' => now()->addHours(71)->toIso8601String(),
            'features' => ['payment', 'mobile', 'ai'],
        ], $overrides));
    }

    /** @param array<string, mixed> $claims */
    private function signedToken(array $claims): string
    {
        $payload = rtrim(strtr(base64_encode((string) json_encode($claims, JSON_THROW_ON_ERROR)), '+/', '-_'), '=');
        self::assertTrue(openssl_sign($payload, $signature, $this->privateKey, OPENSSL_ALGO_SHA256));

        return $payload.'.'.rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    }

    private function forceRemoteCheckDue(): void
    {
        $receipt = json_decode((string) file_get_contents($this->receiptPath), true, flags: JSON_THROW_ON_ERROR);
        self::assertIsArray($receipt);
        $receipt['next_check_at'] = '2000-01-01T00:00:00+00:00';
        file_put_contents($this->receiptPath, json_encode($receipt, JSON_THROW_ON_ERROR));
    }
}
