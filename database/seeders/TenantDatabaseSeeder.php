<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Tenant\ProjectSeeder;
use Database\Seeders\Tenant\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant's database.
     *
     * Creates an initial admin user and sample data for the tenant.
     */
    public function run(): void
    {
        $tenant = tenant();

        if (! $tenant) {
            return;
        }

        $adminEmail = $tenant->admin_email;

        if (! $adminEmail) {
            return;
        }

        // Create the tenant's initial admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => $adminEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed additional demo data
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
