<?php
namespace App\Services;

use App\Models\User;

class FreelancerService
{
    /**
     * List freelancers with filtering using Model Scopes
     */
    public function listFreelancers(array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
    
        return User::query()
            ->freelancers()
            ->with(['freelancerProfile', 'skills'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10);
    }

    /**
     * Get single freelancer details
     */
    public function showFreelancer(int $userId): array
    {
        $freelancer = User::freelancers()
            ->with([
                'freelancerProfile',
                'skills',
                'city.country',
                'reviews.reviewer'
            ])
            ->findOrFail($userId);

        return [
            'profile' => $freelancer,
            'stats' => $this->getStats($freelancer),
        ];
    }

    /**
     * Get Top Rated (Simplified)
     */
    public function getTopRatedFreelancers(int $limit = 10)
    {
        return User::freelancers()
            ->verified()
            ->with(['freelancerProfile', 'skills'])
            ->withAvg('reviews', 'rating')
            ->having('reviews_avg_rating', '>=', 4)
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get Available Freelancers (Simplified)
     */
    public function getAvailableFreelancers(int $limit = 20)
    {
        return User::freelancers()
            ->verified()
            ->available('available')
            ->with(['freelancerProfile', 'skills'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Search by Skills
     */
    public function searchBySkills(array $skillIds, bool $matchAll = false)
    {
        $query = User::freelancers()
            ->verified()
            ->with(['freelancerProfile', 'skills']);

        if ($matchAll) {
            foreach ($skillIds as $id) {
                $query->whereHas('skills', fn($q) => $q->where('skill_id', $id));
            }
        } else {
            $query->whereHas('skills', fn($q) => $q->whereIn('skill_id', $skillIds));
        }

        return $query->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->paginate(20);
    }

    /**
     * Minimal Stats Calculation
     */
    private function getStats(User $freelancer): array
    {
        // Using withCount/withAvg is more efficient than separate queries
        $freelancer->loadCount([
            'bids as completed_projects' => fn($q) => $q->whereHas('project', fn($p) => $p->where('status', 'closed')),
            'reviews'
        ]);
        
        $avgRating = $freelancer->reviews()->avg('rating') ?? 0;

        return [
            'completed_projects' => $freelancer->completed_projects,
            'average_rating' => round($avgRating, 1),
            'total_reviews' => $freelancer->reviews_count,
        ];
    }
}