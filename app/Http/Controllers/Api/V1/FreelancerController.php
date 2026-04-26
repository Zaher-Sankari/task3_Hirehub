<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FreelancerService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    use ApiResponse;

    protected FreelancerService $freelancerService;

    public function __construct(FreelancerService $freelancerService)
    {
        $this->freelancerService = $freelancerService;
    }

    /**
     * List all freelancers with filtering and sorting
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'verified',
                'skill_id',
                'availability',
                'min_rating',
                'min_rate',
                'max_rate',
                'search',
                'sort_by',
                'sort_order',
                'per_page'
            ]);
            
            $freelancers = $this->freelancerService->listFreelancers($filters);
            
            return $this->success($freelancers, 'Freelancers retrieved successfully');
        } catch (Exception $e) {
            return $this->error('Failed to retrieve freelancers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Show specific freelancer details
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $freelancerData = $this->freelancerService->showFreelancer($id);
            return $this->success($freelancerData, 'Freelancer details retrieved successfully');
        } catch (Exception $e) {
            if ($e->getMessage() === 'User is not a freelancer') {
                return $this->error('Freelancer not found', 404);
            }
            return $this->error('Failed to retrieve freelancer details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get top-rated freelancers (for homepage)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function topRated(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $freelancers = $this->freelancerService->getTopRatedFreelancers($limit);
            return $this->success($freelancers, 'Top-rated freelancers retrieved successfully');
        } catch (Exception $e) {
            return $this->error('Failed to retrieve top-rated freelancers', 500);
        }
    }

    /**
     * Get available freelancers (for quick hire)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 20);
            $freelancers = $this->freelancerService->getAvailableFreelancers($limit);
            return $this->success($freelancers, 'Available freelancers retrieved successfully');
        } catch (Exception $e) {
            return $this->error('Failed to retrieve available freelancers', 500);
        }
    }

    /**
     * Search freelancers by skills
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function searchBySkills(Request $request): JsonResponse
    {
        $request->validate([
            'skill_ids' => ['required', 'array'],
            'skill_ids.*' => ['exists:skills,id'],
        ]);
        
        try {
            $freelancers = $this->freelancerService->searchBySkills(
                $request->skill_ids,
                $request->input('match_all', false)
            );
            return $this->success($freelancers, 'Freelancers found successfully');
        } catch (Exception $e) {
            return $this->error('Failed to search freelancers: ' . $e->getMessage(), 500);
        }
    }
}