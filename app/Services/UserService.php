<?php

namespace App\Services;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Get unified profile data for the authenticated user
     */
    public function getProfileData(User $user): array
    {
        $user->load(['city.country', 'freelancerProfile.skills', 'skills']);

        $data = [
            'user' => $user,
            'is_freelancer' => $user->isFreelancer(),
            'is_client' => $user->isClient(),
        ];

        if ($user->isFreelancer()) {
            $data['freelancer_profile'] = $user->freelancerProfile;
            $data['stats'] = $this->getFreelancerStats($user);
        }

        return $data;
    }

    /**
     * Update basic user information
     */
    public function updateBasicInfo(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Update freelancer profile
     * Note: Authorization (Verified Check) is handled in UpdateFreelancerRequest
     */
    public function updateFreelancerProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $profile = Freelancer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $data['bio'] ?? null,
                    'hourly_rate' => $data['hourly_rate'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'availability' => $data['availability'] ?? 'available',
                    'profile_picture' => $data['profile_picture'] ?? null,
                    'portfolio_links' => $data['portfolio_links'] ?? null,
                    'skills_summary' => $data['skills_summary'] ?? null,
                ]
            );

            if (isset($data['skills']) && is_array($data['skills'])) {
                $skillsData = collect($data['skills'])->mapWithKeys(function ($skill) {
                    return [$skill['id'] => ['years_of_experience' => $skill['experience'] ?? 0]];
                })->toArray();

                $profile->skills()->sync($skillsData);
            }

            return $user->load(['freelancerProfile.skills', 'city.country']);
        });
    }

    private function getFreelancerStats(User $user): array
    {
        $completed = $user->bids()->whereHas('project', fn($q) => $q->where('status', 'closed'))->count();
        $active = $user->bids()->where('status', 'accepted')->count();
        $avgRating = $user->reviews()->avg('rating') ?? 0;

        return [
            'completed_projects' => $completed,
            'active_projects' => $active,
            'average_rating' => round($avgRating, 1),
        ];
    }

    public function syncSkills(User $user, array $skills): User
    {
        // Transform input data into pivot format
        $skillsData = collect($skills)->mapWithKeys(function ($skill) {
            return [$skill['id'] => ['years_of_experience' => $skill['experience']]];
        })->toArray();

        // Sync with database
        $user->skills()->sync($skillsData);

        return $user->load('skills');
    }
}
