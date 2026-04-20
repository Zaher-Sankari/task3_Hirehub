<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use App\Models\Project;

class ReviewService
{
    public function storeReview(User $fromUser, array $data)
    {
        $modelClass = $data['reviewable_type'] === 'project' 
                      ? Project::class 
                      : User::class;

        return Review::create([
            'client_id' => $fromUser->id,
            'project_id' => $data['project_id'],
            'reviewable_type'=> $modelClass,
            'reviewable_id'=> $data['reviewable_id'],
            'rating'=> $data['rating'],
            'comment'=> $data['comment'],
        ]);
    }
}