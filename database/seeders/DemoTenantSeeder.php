<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DemoTenantSeeder extends Seeder
{
    /**
     * Seed a demo tenant with fixed credentials for testing.
     *
     * Demo Tenant Credentials:
     * - Domain: demo.{APP_DOMAIN}
     * - Email: admin@demo.com
     * - Password: password
     */
    public function run(): void
    {
        // Check if demo tenant already exists
        if (Tenant::find('demo')) {
            $this->command->info('Demo tenant already exists, skipping...');

            return;
        }

        // Create demo tenant
        $tenant = Tenant::create([
            'id' => 'demo',
            'name' => 'Demo Company',
            'admin_email' => 'admin@demo.com',
            'is_active' => true,
        ]);

        // Create domain (full domain)
        $fullDomain = 'demo.' . config('app.domain');
        $tenant->domains()->create([
            'domain' => $fullDomain,
        ]);

        $this->command->info('');
        $this->command->info('Demo tenant created successfully!');
        $this->command->info('');
        $this->command->info('  URL: https://demo.' . config('app.domain'));
        $this->command->info('');
        $this->command->info('  Demo Users:');
        $this->command->info('  ┌────────────────────┬───────────────────┬──────────┐');
        $this->command->info('  │ Email              │ Role              │ Password │');
        $this->command->info('  ├────────────────────┼───────────────────┼──────────┤');
        $this->command->info('  │ admin@demo.com     │ Admin             │ password │');
        $this->command->info('  │ john@demo.com      │ User              │ password │');
        $this->command->info('  │ jane@demo.com      │ User              │ password │');
        $this->command->info('  └────────────────────┴───────────────────┴──────────┘');
        $this->command->info('');
    }
}
