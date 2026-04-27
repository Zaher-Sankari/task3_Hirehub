<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Jobs\AddProjectNotification;
use App\Models\Project;
use App\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * List projects with filters (15 per page)
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->query();
        $projects = $this->projectService->listProjects($filters);
        return $this->success($projects);
    }

    /**
     * Create a new project
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->storeProject(
            $request->user(), 
            $request->validated()
        );
        AddProjectNotification::dispatch($project);
        return $this->success($project, 'Project created successfully', 201);
    }

    /**
     * Show project details with all relationships
     */
    public function show(Project $project): JsonResponse
    {
        // Load relationships with eager loading to prevent N+1
        $project->load([
            'client',
            'bids' => function ($query) {
                $query->with(['freelancer.freelancerProfile', 'freelancer.skills'])
                    ->latest();
            },
            'tags',
            'attachments',
            'reviews' => function ($query) {
                $query->with('reviewable')->latest();
            }
        ]);
        
        // Add computed fields
        $data = $project->toArray();
        $data['bids_count'] = $project->bids->count();
        $data['avg_rating'] = $project->reviews->avg('rating') ?? 0;
        
        return $this->success($data);
    }

    /**
     * Update the project
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $updatedProject = $this->projectService->updateProject($project, $request->validated());
        return $this->success($updatedProject, 'Project updated successfully');
    }

    /**
     * Delete a project
     */
    public function destroy(Project $project, Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($project->client_id !== $user->id) {
            return $this->error('You are not authorized to delete this project', 403);
        }
        
        // Only allow deletion if no bids accepted
        $hasAcceptedBid = $project->bids()->where('status', 'accepted')->exists();
        if ($hasAcceptedBid) {
            return $this->error('Cannot delete project with an accepted bid', 400);
        }
        
        $this->projectService->deleteProject($project);
        return $this->success(null, 'Project deleted successfully');
    }

    /**
     * Mark project as closed
     */
    public function markAsClosed(Project $project, Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($project->client_id !== $user->id) {
            return $this->error('You are not authorized to close this project', 403);
        }
        
        if ($project->status === 'closed') {
            return $this->error('Project is already closed', 400);
        }
        
        $project->update(['status' => 'closed']);
        
        return $this->success($project, 'Project closed successfully');
    }
}