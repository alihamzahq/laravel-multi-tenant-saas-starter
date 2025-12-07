<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Tenant\Auth\NewPasswordController;
use App\Http\Controllers\Tenant\Auth\PasswordResetLinkController;
use App\Http\Controllers\Tenant\Auth\RegisteredUserController;
use App\Http\Controllers\Tenant\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Root redirect - accessible to all
    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('tenant.dashboard');
        }
        return redirect()->route('tenant.login');
    });

    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('tenant.login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('register', [RegisteredUserController::class, 'create'])
            ->name('tenant.register');
        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('tenant.password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('tenant.password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('tenant.password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('tenant.password.store');
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('tenant.logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard');

        // Placeholder routes for navigation (will be implemented in next phases)
        Route::get('/projects', function () {
            return inertia('Tenant/Projects/Index');
        })->name('tenant.projects.index');

        Route::get('/profile', function () {
            return inertia('Tenant/Profile/Edit');
        })->name('tenant.profile.edit');
    });
});
