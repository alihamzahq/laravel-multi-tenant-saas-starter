<?php

declare(strict_types=1);

namespace Database\Seeders\Tenant;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Seed sample projects for the tenant.
     */
    public function run(): void
    {
        // Get users by email (admin is created first by TenantDatabaseSeeder)
        $admin = User::where('role', 'admin')->first();
        $john = User::where('email', 'john@demo.com')->first();
        $jane = User::where('email', 'jane@demo.com')->first();

        $projects = [
            [
                'name' => 'Website Redesign',
                'description' => 'Complete overhaul of company website with modern design and improved UX.',
                'status' => 'active',
                'created_by' => $admin?->id,
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Native iOS and Android applications for customer engagement.',
                'status' => 'active',
                'created_by' => $john?->id ?? $admin?->id,
            ],
            [
                'name' => 'API Integration',
                'description' => 'Third-party API integrations for payment processing and analytics.',
                'status' => 'completed',
                'created_by' => $jane?->id ?? $admin?->id,
            ],
            [
                'name' => 'Database Migration',
                'description' => 'Migrate legacy database to new optimized schema.',
                'status' => 'draft',
                'created_by' => $admin?->id,
            ],
            [
                'name' => 'Security Audit',
                'description' => 'Annual security review and penetration testing.',
                'status' => 'archived',
                'created_by' => $john?->id ?? $admin?->id,
            ],
        ];

        foreach ($projects as $projectData) {
            if ($projectData['created_by']) {
                Project::updateOrCreate(
                    ['name' => $projectData['name']],
                    $projectData
                );
            }
        }
    }
}
