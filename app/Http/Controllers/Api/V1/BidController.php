<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BidService;
use App\Http\Requests\StoreBidRequest;
use App\Models\Bid;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BidController extends Controller
{
    use ApiResponse;

    protected $bidService;

    public function __construct(BidService $bidService)
    {
        $this->bidService = $bidService;
    }


    public function store(StoreBidRequest $request): JsonResponse
    {
        $bid = $this->bidService->placeBid($request->user(), $request->validated());
        return $this->success($bid, 'Successfully added your bid', 201);
    }

    public function accept($id, Request $request): JsonResponse
    {
        $bid = Bid::with('project')->findOrFail($id);

        if ($bid->project->client_id !== $request->user()->id) {
            return $this->error('You cant accpet this offer', 403);
        }

        $acceptedBid = $this->bidService->acceptBid($bid);
        return $this->success($acceptedBid, 'Offer accepted.');
    }

    public function reject($id, Request $request): JsonResponse
    {
        try {
            $bid = Bid::with('project')->findOrFail($id);

            if ($bid->project->client_id !== $request->user()->id) {
                return $this->error('You cant reject this offer', 403);
            }

            $rejectedBid = $this->bidService->rejectBid($bid);
            return $this->success($rejectedBid, 'Offer rejected.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

}