<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function test_is_editable_by_creator(): void
    {
        $user = new User(['role' => 'user']);
        $user->id = 1;

        $project = new Project(['created_by' => 1]);

        $this->assertTrue($project->isEditableBy($user));
    }

    public function test_is_editable_by_tenant_admin_even_if_not_creator(): void
    {
        $admin = new User(['role' => 'admin']);
        $admin->id = 2;

        $project = new Project(['created_by' => 1]);

        $this->assertTrue($project->isEditableBy($admin));
    }

    public function test_is_not_editable_by_non_creator_non_admin(): void
    {
        $user = new User(['role' => 'user']);
        $user->id = 2;

        $project = new Project(['created_by' => 1]);

        $this->assertFalse($project->isEditableBy($user));
    }
}
