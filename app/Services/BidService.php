<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class BidService
{

    public function placeBid(User $user, array $data)
    {
        return $user->bids()->create($data);
    }

    public function acceptBid(Bid $bid)
    {
        return DB::transaction(function () use ($bid) {
            $bid->update(['status' => 'accepted']);
            $bid->project->update(['status' => 'in_progress']);
            $bid->project->bids()->where('id', '!=', $bid->id)->update(['status' => 'rejected']);

            return $bid;
        });
    }
    public function rejectBid(Bid $bid): Bid
    {
        if ($bid->status === 'accepted') {
            throw new Exception('Cannot reject an accepted bid');
        }

        return DB::transaction(function () use ($bid) {
            $bid->update(['status' => 'rejected']);
            return $bid;
        });
    }
}