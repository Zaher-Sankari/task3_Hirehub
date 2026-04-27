<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProjectService
{
    /**
     * List projects with filters (15 per page)
     */
    public function listProjects(array $filters)
    {
        $cacheKey = 'project_ids_' . md5(json_encode($filters));

        // Cache only the IDs
        $projectIds = Cache::remember($cacheKey, 180, function () use ($filters) {
            $query = Project::query()
                ->with(['client', 'tags', 'bids'])
                ->where('status', 'open');

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($filters['min_budget'])) {
                $query->where('budget', '>=', $filters['min_budget']);
            }

            if (!empty($filters['max_budget'])) {
                $query->where('budget', '<=', $filters['max_budget']);
            }

            if (!empty($filters['budget_type'])) {
                $query->where('budget_type', $filters['budget_type']);
            }

            if (!empty($filters['tag_ids'])) {
                $tagIds = is_array($filters['tag_ids']) ? $filters['tag_ids'] : explode(',', $filters['tag_ids']);
                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            }

            return $query->pluck('id')->toArray();
        });

        $projects = Project::whereIn('id', $projectIds)
            ->with(['client', 'tags', 'bids'])
            ->paginate(15);

        return $projects;
    }
    /**
     * Create a new project
     */
    public function storeProject(User $user, array $data): Project
    {
        return DB::transaction(function () use ($user, $data) {
            $project = $user->projects()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'budget_type' => $data['budget_type'],
                'budget' => $data['budget'],
                'deadline' => $data['deadline'],
                'status' => 'open',
            ]);

            if (!empty($data['tags'])) {
                $project->tags()->sync($data['tags']);
            }
            Cache::flush();
            return $project->load(['client', 'tags']);
        });
    }

    /**
     * Find a specific project with all relations
     */
    public function findProject(int $id): Project
    {
        return Project::with([
            'client',
            'tags',
            'bids' => function ($query) {
                $query->with(['freelancer.freelancerProfile', 'freelancer.skills'])
                    ->latest();
            },
            'attachments',
            'reviews' => function ($query) {
                $query->with('reviewable')->latest();
            }
        ])->findOrFail($id);
    }

    /**
     * Update an existing project
     */
    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            // Only allow update if project is open
            if ($project->status !== 'open') {
                throw new \Exception('Cannot update a closed or in-progress project');
            }

            $project->update($data);

            if (isset($data['tags'])) {
                $project->tags()->sync($data['tags']);
            }
            Cache::flush();
            return $project->load(['client', 'tags']);
        });
    }

    /**
     * Delete a project
     */
    public function deleteProject(Project $project): bool
    {
        // Check if project has any accepted bids
        $hasAcceptedBid = $project->bids()->where('status', 'accepted')->exists();

        if ($hasAcceptedBid) {
            throw new \Exception('Cannot delete a project with an accepted bid');
        }

        return DB::transaction(function () use ($project) {
            $project->tags()->detach();
            $project->bids()->delete();
            $project->attachments()->delete();

            Cache::flush();
            return $project->delete();
        });
    }

    /**
     * Close a project
     */
    public function closeProject(Project $project): Project
    {
        if ($project->status === 'closed') {
            throw new \Exception('Project is already closed');
        }

        $project->update(['status' => 'closed']);

        Cache::flush();
        return $project;
    }
}
