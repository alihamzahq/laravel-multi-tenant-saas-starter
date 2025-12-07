<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shared = [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'app' => [
                'name' => config('app.name'),
                'domain' => config('app.domain'),
            ],
        ];

        // Share tenant data when in tenant context
        if (tenancy()->initialized && ($tenant = tenant())) {
            $shared['tenant'] = [
                'id' => $tenant->id,
                'name' => $tenant->name,
            ];
        }

        return $shared;
    }
}
