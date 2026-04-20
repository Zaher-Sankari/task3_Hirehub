<?php

namespace App\Services;

use App\Models\Freelancer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function updateBasicInfo(User $user, array $data)
    {
        $user->update($data);

        return $user;
    }
    public function updateFreelancerProfile(User $user, array $data): User
    {
        if (!$user->freelancerProfile->is_verified) {
            throw new Exception('Freelancer profile is not verified');
        }

        return DB::transaction(function () use ($user, $data) {
            $profile = Freelancer::updateOrCreate(
                ['user_id' => $user->id],
                array_intersect_key($data, array_flip(['bio', 'hourly_rate', 'phone', 'availability']))
            );

            if (isset($data['skills'])) {
                $skillsData = [];
                foreach ($data['skills'] as $skill) {
                    $skillsData[$skill['id']] = ['years_of_experience' => $skill['experience']];
                }
                $profile->skills()->sync($skillsData);
            }

            return $user->load('freelancerProfile.skills');
        });
    }

    public function isFreelancer(User $user)
    {
        // check if use is not freelancer, then return error
        if ($user->type !== 'freelancer') {
            throw new Exception('User is not a freelancer');
        }
    }
}
