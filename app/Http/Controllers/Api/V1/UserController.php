<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['city.country', 'freelancerProfile.skills']);
        $this->userService->isFreelancer($user);
        return $this->success($user);
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->updateBasicInfo($request->user(), $request->validated());
        return $this->success($user,'Updated basic info successfully');
    }

    public function updateFreelancerProfile(UpdateFreelancerRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->updateFreelancerProfile($request->user(), $request->validated());
            return $this->success($user,'Updated freelancer profile successfully');
        } catch (\Exception $e) {
            return $this->error('An error occurred while updating freelancer profile', 500, $e->getMessage());
        }
    }
}
