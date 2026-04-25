<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\Freelancer;
use App\Models\Review;
use App\Models\User;

class StatsService
{
    public function getDashboardStats(): array
    {
        return [
            'users' => $this->getUserStats(),
            'reviews' => $this->getReviewStats(),
            'finance' => $this->getFinancialStats(),
        ];
    }

    private function getUserStats(): array
    {
        return [
            'total_clients' => User::where('type', 'client')->count(),
            'total_freelancers' => User::where('type', 'freelancer')->count(),
            'verified_freelancers' => Freelancer::where('verified', true)->count(),
            'unverified_freelancers' =>Freelancer::where('verified', false)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];
    }
    private function getReviewStats(): array
    {
        return [
            'average_rating' => round(Review::avg('rating') ?? 0, 1),
            'total_reviews' => Review::count(),
            'reviews_this_week' => Review::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    private function getFinancialStats(): array
    {
        return [
            'total_transactions_value' => Bid::where('status', 'accepted')->sum('amount'),
            'average_bid_amount' => round(Bid::avg('amount') ?? 0, 2),
            'total_bids_value' => round(Bid::sum('amount'), 2),
        ];
    }
}