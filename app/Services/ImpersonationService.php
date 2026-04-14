<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use InvalidArgumentException;

class ImpersonationService
{
    /**
     * Token expiry time in minutes.
     */
    private const TOKEN_EXPIRY_MINUTES = 5;

    /**
     * Generate an impersonation token for a tenant.
     */
    public function generateToken(Tenant $tenant): string
    {
        $expiry = now()->addMinutes(self::TOKEN_EXPIRY_MINUTES)->timestamp;
        $payload = "{$tenant->id}|{$expiry}";

        return base64_encode($payload).'.'.$this->hash($payload);
    }

    /**
     * Generate a signature for the token.
     */
    public function generateSignature(string $tenantId, string $token): string
    {
        return $this->hash("{$tenantId}|{$token}");
    }

    /**
     * Verify the signature matches.
     */
    public function verifySignature(string $tenantId, string $token, string $signature): bool
    {
        $expectedSignature = $this->generateSignature($tenantId, $token);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify and decode a token.
     *
     * @return array{tenant_id: string, expiry: int}
     *
     * @throws InvalidArgumentException
     */
    public function verifyToken(string $token, string $expectedTenantId): array
    {
        $tokenParts = explode('.', $token);

        if (count($tokenParts) !== 2) {
            throw new InvalidArgumentException('Invalid token format.');
        }

        [$encodedPayload, $tokenHash] = $tokenParts;
        $payload = base64_decode($encodedPayload);

        // Verify token hash
        if (! hash_equals($this->hash($payload), $tokenHash)) {
            throw new InvalidArgumentException('Invalid token.');
        }

        // Parse payload
        $payloadParts = explode('|', $payload);

        if (count($payloadParts) !== 2) {
            throw new InvalidArgumentException('Invalid token payload.');
        }

        [$tenantId, $expiry] = $payloadParts;

        // Verify tenant ID matches
        if ($tenantId !== $expectedTenantId) {
            throw new InvalidArgumentException('Token tenant mismatch.');
        }

        // Check expiry
        if ((int) $expiry < now()->timestamp) {
            throw new InvalidArgumentException('Impersonation link has expired.');
        }

        return [
            'tenant_id' => $tenantId,
            'expiry' => (int) $expiry,
        ];
    }

    /**
     * Build the full impersonation URL for a tenant.
     */
    public function buildImpersonationUrl(Tenant $tenant): string
    {
        $domain = $tenant->domains->first()?->domain;

        if (! $domain) {
            throw new InvalidArgumentException('Tenant does not have a domain configured.');
        }

        $token = $this->generateToken($tenant);
        $signature = $this->generateSignature($tenant->id, $token);

        $protocol = request()->secure() ? 'https' : 'http';

        return "{$protocol}://{$domain}/impersonate?".http_build_query([
            'token' => $token,
            'signature' => $signature,
        ]);
    }

    /**
     * Generate HMAC hash using the app key.
     */
    private function hash(string $data): string
    {
        return hash_hmac('sha256', $data, config('app.key'));
    }
}
