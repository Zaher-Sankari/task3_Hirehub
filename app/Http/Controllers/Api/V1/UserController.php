<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Http\Requests\UpdateSkillsRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Skill;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $data = $this->userService->getProfileData($request->user());
        return $this->success($data, 'Profile retrieved successfully');
    }

    /**
     * Update basic user info
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->updateBasicInfo($request->user(), $request->validated());
        return $this->success($user, 'Basic info updated successfully');
    }

    /**
     * Update freelancer profile
     * Authorization (Verified Check) is handled in UpdateFreelancerRequest::authorize()
     */
    public function updateFreelancerProfile(UpdateFreelancerRequest $request): JsonResponse
    {
        $user = $this->userService->updateFreelancerProfile($request->user(), $request->validated());
        return $this->success($user, 'Freelancer profile updated successfully');
    }

    /**
     * Sync user skills
     */
    public function updateSkills(UpdateSkillsRequest $request): JsonResponse
    {
        $user = $this->userService->syncSkills($request->user(), $request->validated()['skills']);
        return $this->success($user, 'Skills updated successfully');
    }

    /**
     * Remove a specific skill
     */
    public function removeSkill(Skill $skill, Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Detach directly. If it doesn't exist, Laravel does nothing (safe).
        $user->skills()->detach($skill->id);

        return $this->success(null, 'Skill removed successfully');
    }
}