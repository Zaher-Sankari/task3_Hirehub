<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    use ApiResponse;

    protected StatsService $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
        
        // Admin-only access
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * Get dashboard statistics (founders only)
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->statsService->getDashboardStats();
            return $this->success($data, 'Dashboard stats retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Stats retrieval failed', ['error' => $e->getMessage()]);
            return $this->error('Failed to retrieve statistics', 500);
        }
    }
}