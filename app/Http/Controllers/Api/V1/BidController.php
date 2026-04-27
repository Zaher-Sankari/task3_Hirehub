<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBidRequest;
use App\Jobs\AcceptBidNNotification;
use App\Jobs\RejectBidNNotification;
use App\Models\Bid;
use App\Services\BidService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BidController extends Controller
{
    use ApiResponse;

    protected BidService $bidService;

    public function __construct(BidService $bidService)
    {
        $this->bidService = $bidService;
    }

    public function store(StoreBidRequest $request): JsonResponse
    {
        $bid = $this->bidService->placeBid(
            $request->user(),
            $request->validated(),
            $request->route('project')
        );
        
        return $this->success($bid, 'Proposal submitted successfully', 201);
    }

    public function show( Bid $bid): JsonResponse
    {
        $bid = $this->bidService->getBidWithRelations($bid);
        
        return $this->success($bid, 'Bid details retrieved successfully');
    }


    public function accept(Bid $bid): JsonResponse
    {
        $acceptedBid = $this->bidService->acceptBid($bid);

        AcceptBidNNotification::dispatch($acceptedBid);
        
        return $this->success($acceptedBid, 'Bid accepted successfully');
    }

    public function reject(Bid $bid): JsonResponse
    {
        $rejectedBid = $this->bidService->rejectBid($bid);

        RejectBidNNotification::dispatch($rejectedBid);
        
        return $this->success($rejectedBid, 'Bid rejected successfully');
    }
}