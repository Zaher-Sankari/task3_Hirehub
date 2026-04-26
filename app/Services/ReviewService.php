<?php
namespace App\Services;

use App\Models\Freelancer;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Store a new review
     * Note: Authorization and Validation are handled by StoreReviewRequest
     */
    public function storeReview(User $reviewer, array $data): Review
    {
        return DB::transaction(function () use ($reviewer, $data) {
            // Determine model class
            $modelClass = $data['reviewable_type'] === 'project' 
                ? Project::class 
                : Freelancer::class;

            $review = Review::create([
                'reviewer_id' => $reviewer->id,
                'project_id' => $data['project_id'],
                'reviewable_type' => $modelClass,
                'reviewable_id' => $data['reviewable_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment'],
            ]);

            return $review->load('reviewer');
        });
    }
}