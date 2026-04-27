<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class FreelancerService
{
    /**
     * List freelancers with filtering using Model Scopes
     */
    public function listFreelancers(array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
    
        $cacheKey = 'freelancers_list_' . $sortBy . '_' . $sortOrder;
        return Cache::remember($cacheKey, 600, function() use ($sortBy, $sortOrder){
            return User::query()
            ->freelancers()
            ->with(['freelancerProfile', 'skills'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate(10);
        });
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
     * Get Top Rated
     */
    public function getTopRatedFreelancers(int $limit = 10)
    {
        $cacheKey = 'top_rated_freelancers_' . $limit;
         return Cache::remember($cacheKey, 300, function() use ($limit) {
        return User::freelancers()
            ->verified()
            ->with(['freelancerProfile', 'skills'])
            ->withAvg('reviews', 'rating')
            ->having('reviews_avg_rating', '>=', 4)
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    });
    }
    /**
     * Get Available Freelancers
     */
    public function getAvailableFreelancers(int $limit = 20)
    {

     $cacheKey = 'available_freelancers_' . $limit;
        
        return Cache::remember($cacheKey, 300, function() use ($limit) {
        return User::freelancers()
            ->verified()
            ->available('available')
            ->with(['freelancerProfile', 'skills'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit($limit)
            ->get();
    });}

    /**
     * Search by Skills
     */
    public function searchBySkills(array $skillIds, bool $matchAll = false)
    {
        $cacheKey = 'skills_search_' . implode('_', $skillIds) . '_' . ($matchAll ? 'all' : 'any');
         return Cache::remember($cacheKey, 300, function() use ($skillIds, $matchAll) {
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
    });}

    private function getStats(User $freelancer): array
    {
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