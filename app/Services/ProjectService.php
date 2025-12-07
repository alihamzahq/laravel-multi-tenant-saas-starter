<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    /**
     * Create a new project.
     *
     * @param array<string, mixed> $data
     */
    public function createProject(array $data, int $createdBy): Project
    {
        return Project::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Update an existing project.
     *
     * @param array<string, mixed> $data
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? $project->description,
            'status' => $data['status'] ?? $project->status,
        ]);

        return $project->fresh();
    }

    /**
     * Delete a project.
     */
    public function deleteProject(Project $project): bool
    {
        return $project->delete();
    }
}
