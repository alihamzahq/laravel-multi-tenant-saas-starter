<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Tenant\Auth\NewPasswordController;
use App\Http\Controllers\Tenant\Auth\PasswordResetLinkController;
use App\Http\Controllers\Tenant\Auth\RegisteredUserController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\ProjectController;
use App\Http\Controllers\Tenant\UserController;
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
        // Login
        Route::controller(AuthenticatedSessionController::class)->group(function () {
            Route::get('login', 'create')->name('tenant.login');
            Route::post('login', 'store');
        });

        // Register
        Route::controller(RegisteredUserController::class)->group(function () {
            Route::get('register', 'create')->name('tenant.register');
            Route::post('register', 'store');
        });

        // Password Reset
        Route::controller(PasswordResetLinkController::class)->group(function () {
            Route::get('forgot-password', 'create')->name('tenant.password.request');
            Route::post('forgot-password', 'store')->name('tenant.password.email');
        });

        Route::controller(NewPasswordController::class)->group(function () {
            Route::get('reset-password/{token}', 'create')->name('tenant.password.reset');
            Route::post('reset-password', 'store')->name('tenant.password.store');
        });
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('tenant.logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('tenant.dashboard');

        // Users - Index accessible to all, CRUD restricted to tenant admins
        Route::controller(UserController::class)
            ->prefix('users')
            ->name('tenant.users.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::middleware('tenant.admin')->group(function () {
                    Route::post('/', 'store')->name('store');
                    Route::put('/{user}', 'update')->name('update');
                    Route::delete('/{user}', 'destroy')->name('destroy');
                });
            });

        // Projects - Full CRUD for all authenticated users
        Route::resource('projects', ProjectController::class)
            ->names('tenant.projects');

        // Profile
        Route::controller(ProfileController::class)
            ->prefix('profile')
            ->name('tenant.profile.')
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::patch('/', 'update')->name('update');
                Route::put('/password', 'updatePassword')->name('password');
            });
    });
});
