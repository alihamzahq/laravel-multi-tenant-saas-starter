<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with domain.
     *
     * Note: We don't wrap this in a DB transaction because Stancl/Tenancy
     * fires events that trigger jobs (CreateDatabase, etc.) which manage
     * their own transactions.
     *
     * @param array<string, mixed> $data
     */
    public function createTenant(array $data): Tenant
    {
        // Build full domain from subdomain + app domain
        $subdomain = $data['domain'];
        $fullDomain = $subdomain . '.' . config('app.domain');

        $tenant = Tenant::create([
            'id' => $subdomain,
            'name' => $data['name'],
            'admin_email' => $data['admin_email'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $tenant->domains()->create([
            'domain' => $fullDomain,
        ]);

        return $tenant->load('domains');
    }

    /**
     * Update an existing tenant.
     *
     * @param array<string, mixed> $data
     */
    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update([
            'name' => $data['name'],
            'admin_email' => $data['admin_email'] ?? $tenant->admin_email,
            'is_active' => $data['is_active'] ?? $tenant->is_active,
        ]);

        return $tenant->fresh()->load('domains');
    }

    /**
     * Delete a tenant and its associated resources.
     *
     * Note: Stancl/Tenancy handles database deletion via events.
     */
    public function deleteTenant(Tenant $tenant): bool
    {
        // Delete all domains first
        $tenant->domains()->delete();

        // Delete the tenant (this will also drop the tenant database via Stancl)
        return $tenant->delete();
    }

    /**
     * Toggle the active status of a tenant.
     */
    public function toggleStatus(Tenant $tenant): Tenant
    {
        $tenant->update([
            'is_active' => ! $tenant->is_active,
        ]);

        return $tenant->fresh();
    }

    /**
     * Generate a unique tenant ID from the name.
     */
    public function generateTenantId(string $name): string
    {
        $baseId = Str::slug($name);
        $id = $baseId;
        $counter = 1;

        while (Tenant::where('id', $id)->exists()) {
            $id = $baseId . '-' . $counter;
            $counter++;
        }

        return $id;
    }
}
