<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_is_admin_returns_true_when_is_admin_flag_is_true(): void
    {
        $user = new User(['is_admin' => true]);

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_false_when_is_admin_flag_is_false(): void
    {
        $user = new User(['is_admin' => false]);

        $this->assertFalse($user->isAdmin());
    }

    public function test_is_tenant_admin_returns_true_for_admin_role(): void
    {
        $user = new User(['role' => 'admin']);

        $this->assertTrue($user->isTenantAdmin());
    }

    public function test_is_tenant_admin_returns_false_for_user_role(): void
    {
        $user = new User(['role' => 'user']);

        $this->assertFalse($user->isTenantAdmin());
    }

    public function test_has_role_matches_the_role_attribute(): void
    {
        $user = new User(['role' => 'admin']);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('user'));
    }
}
