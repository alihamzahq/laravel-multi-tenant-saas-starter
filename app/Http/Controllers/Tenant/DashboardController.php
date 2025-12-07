<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard.
     */
    public function index(Request $request): Response
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalProjects' => Project::count(),
            'activeProjects' => Project::active()->count(),
            'draftProjects' => Project::draft()->count(),
        ];

        $recentProjects = Project::with('creator')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'status_label' => Project::STATUSES[$project->status] ?? $project->status,
                'creator' => $project->creator?->name ?? 'Unknown',
                'created_at' => $project->created_at->format('M d, Y'),
            ]);

        return Inertia::render('Tenant/Dashboard', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
        ]);
    }
}
