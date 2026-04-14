<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreProjectRequest;
use App\Http\Requests\Tenant\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tenant API Project Controller.
 *
 * Handles CRUD operations for project management via API.
 */
class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Display a listing of projects.
     */
    public function index(Request $request): JsonResponse
    {
        $projects = Project::with('creator')
            ->latest()
            ->paginate(15);

        return $this->paginated(
            ProjectResource::collection($projects),
            'Projects retrieved successfully'
        );
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject(
            $request->validated(),
            $request->user()->id
        );

        $project->load('creator');

        return $this->created(
            new ProjectResource($project),
            'Project created successfully'
        );
    }

    /**
     * Display the specified project.
     */
    public function show(int $id): JsonResponse
    {
        $project = Project::with('creator')->findOrFail($id);

        return $this->success(
            new ProjectResource($project),
            'Project retrieved successfully'
        );
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        // Check if user can edit this project
        if (! $project->isEditableBy($request->user())) {
            return $this->error('You are not authorized to edit this project', null, 403);
        }

        $project = $this->projectService->updateProject($project, $request->validated());
        $project->load('creator');

        return $this->success(
            new ProjectResource($project),
            'Project updated successfully'
        );
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        // Check if user can delete this project
        if (! $project->isEditableBy($request->user())) {
            return $this->error('You are not authorized to delete this project', null, 403);
        }

        $this->projectService->deleteProject($project);

        return $this->noContent('Project deleted successfully');
    }
}
