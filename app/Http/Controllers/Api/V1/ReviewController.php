<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Services\ReviewService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    use ApiResponse;

    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Store a new review
     * Authorization & Validation handled by StoreReviewRequest
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = $this->reviewService->storeReview(
            $request->user(),
            $request->validated()
        );

        return $this->success($review, 'Review created successfully', 201);
    }
}