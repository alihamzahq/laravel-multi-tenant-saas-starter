<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreProjectRequest;
use App\Http\Requests\Tenant\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    /**
     * Display a listing of projects.
     */
    public function index(): Response
    {
        $projects = Project::with('creator:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'status_label' => Project::STATUSES[$project->status] ?? $project->status,
                'creator' => $project->creator?->name ?? 'Unknown',
                'created_at' => $project->created_at->format('M d, Y'),
                'can_edit' => $project->isEditableBy(auth()->user()),
            ]);

        return Inertia::render('Tenant/Projects/Index', [
            'projects' => $projects,
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): Response
    {
        return Inertia::render('Tenant/Projects/Create', [
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projectService->createProject(
            $request->validated(),
            auth()->id()
        );

        return redirect()
            ->route('tenant.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): Response
    {
        $project->load('creator:id,name');

        return Inertia::render('Tenant/Projects/Show', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'status_label' => Project::STATUSES[$project->status] ?? $project->status,
                'creator' => $project->creator?->name ?? 'Unknown',
                'created_at' => $project->created_at->format('M d, Y \a\t g:i A'),
                'updated_at' => $project->updated_at->format('M d, Y \a\t g:i A'),
                'can_edit' => $project->isEditableBy(auth()->user()),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): Response
    {
        if (! $project->isEditableBy(auth()->user())) {
            abort(403, 'You do not have permission to edit this project.');
        }

        return Inertia::render('Tenant/Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
            ],
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->updateProject($project, $request->validated());

        return redirect()
            ->route('tenant.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): RedirectResponse
    {
        if (! $project->isEditableBy(auth()->user())) {
            return redirect()
                ->route('tenant.projects.index')
                ->with('error', 'You do not have permission to delete this project.');
        }

        $this->projectService->deleteProject($project);

        return redirect()
            ->route('tenant.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
