<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Tenant;
use App\Services\ImpersonationService;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Tests\TestCase;

class ImpersonationServiceTest extends TestCase
{
    private ImpersonationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

        $this->service = new ImpersonationService;
    }

    public function test_generate_token_returns_signed_payload(): void
    {
        $tenant = new Tenant(['id' => 'acme']);

        $token = $this->service->generateToken($tenant);

        $this->assertStringContainsString('.', $token);
        $this->assertCount(2, explode('.', $token));
    }

    public function test_verify_token_succeeds_for_valid_token(): void
    {
        $tenant = new Tenant(['id' => 'acme']);
        $token = $this->service->generateToken($tenant);

        $result = $this->service->verifyToken($token, 'acme');

        $this->assertSame('acme', $result['tenant_id']);
        $this->assertIsInt($result['expiry']);
    }

    public function test_verify_token_rejects_expired_token(): void
    {
        $tenant = new Tenant(['id' => 'acme']);
        $token = $this->service->generateToken($tenant);

        Carbon::setTestNow(now()->addMinutes(10));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Impersonation link has expired.');

        $this->service->verifyToken($token, 'acme');
    }

    public function test_verify_token_rejects_tenant_mismatch(): void
    {
        $tenant = new Tenant(['id' => 'acme']);
        $token = $this->service->generateToken($tenant);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token tenant mismatch.');

        $this->service->verifyToken($token, 'other-tenant');
    }

    public function test_verify_token_rejects_tampered_signature(): void
    {
        $tenant = new Tenant(['id' => 'acme']);
        $token = $this->service->generateToken($tenant);

        [$payload] = explode('.', $token);
        $tamperedToken = $payload.'.'.str_repeat('a', 64);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid token.');

        $this->service->verifyToken($tamperedToken, 'acme');
    }

    public function test_verify_signature_rejects_wrong_signature(): void
    {
        $result = $this->service->verifySignature('acme', 'some-token', 'wrong-signature');

        $this->assertFalse($result);
    }

    public function test_verify_signature_accepts_matching_signature(): void
    {
        $signature = $this->service->generateSignature('acme', 'some-token');

        $result = $this->service->verifySignature('acme', 'some-token', $signature);

        $this->assertTrue($result);
    }
}
