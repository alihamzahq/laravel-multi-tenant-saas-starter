<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Central Domain Routes
|--------------------------------------------------------------------------
|
| Routes for the central application (main domain).
| Only admin routes are handled here. User authentication is on tenant domains.
|
*/

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return Inertia::render('Welcome', [
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        });

        // Redirect central /login to admin login
        Route::get('/login', function () {
            return redirect()->route('admin.login');
        })->name('login');

        require __DIR__.'/admin.php';
    });
}
