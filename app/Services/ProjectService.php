<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function listProjects(array $filters)
    {
        $projects = Project::open()
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->search($s))
            ->with(['client', 'tags'])
            ->latest()
            ->paginate(15);

        if ($projects->isEmpty()) {
            return [
                'message' => 'No open projects available',
                'projects' => [],
            ];
        }

        return $projects;
    }

    public function storeProject(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $project = $user->projects()->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'budget' => $data['budget'],
                'deadline' => $data['deadline'],
                'status' => 'open',
            ]);

            if (! empty($data['tags'])) {
                $project->tags()->sync($data['tags']);
            }

            return $project->load('tags');
        });
    }
// show specific project with proposals, attachments and reviews:  
    public function findProject(int $id)
    {
        return Project::with(['client', 'tags', 'bids.freelancer'])->findOrFail($id);
    }

    public function updateProject(Project $project, array $data)
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update($data);

            if (isset($data['tags'])) {
                $project->tags()->sync($data['tags']);
            }

            return $project->load('tags');
        });
    }

    public function deleteProject(Project $project)
    {
        return $project->delete();
    }
}
