<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->listProjects($request->all());
        return $this->success($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->storeProject(
            $request->user(), 
            $request->validated()
        );

        return $this->success($project, 'Project created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        try {
            $project = $this->projectService->findProject($id);
            return $this->success($project);
        } catch (\Exception $e) {
            return $this->error('Project not found', 404);
        }
    }
    /**
 * Update the project
 */
public function update(UpdateProjectRequest $request, Project $project): JsonResponse
{
    
    $updatedProject = $this->projectService->updateProject($project, $request->validated());

    return $this->success($updatedProject, 'Project updated successfully');
}



public function destroy(Request $request, $id): JsonResponse
{
    $project = Project::findOrFail($id);

    if ($project->client_id !== $request->user()->id) {
        return $this->error('You are not allowed to delete this project', 403);
    }

    $this->projectService->deleteProject($project);

    return $this->success(null, 'Project deleted successfully');
}

public function markAsClosed($id, Request $request): JsonResponse
{
    $project = Project::findOrFail($id);

    if ($project->client_id !== $request->user()->id) {
        return $this->error('You are not allowed to close this project', 403);
    }

    $project->update(['status' => 'closed']);

    return $this->success($project, 'Project marked as closed');
}
}