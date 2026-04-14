<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'id' => 'company1',
            'data' => ['name' => 'Company 1'],
        ]);
        $tenant->domains()->create(['domain' => 'company1.localhost']);
    }
}
