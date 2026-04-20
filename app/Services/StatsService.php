<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatsService
{
    public function getDashboardStats(): array
    {
        return [
            'users' => $this->getUserStats(),
            'projects' => $this->getProjectStats(),
            'reviews' => $this->getReviewStats(),
            'finance' => $this->getFinancialStats(),
            'activity' => $this->getActivityStats(),
        ];
    }

    private function getUserStats(): array
    {
        return [
            'total_clients' => User::where('type', 'client')->count(),
            'total_freelancers' => User::where('type', 'freelancer')->count(),
            'verified_freelancers' => User::where('type', 'freelancer')->where('is_verified', true)->count(),
        ];
    }

    private function getProjectStats()
    {
        return Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
    }

    private function getReviewStats(): array
    {
        return [
            'average' => round(Review::avg('rating') ?? 0, 1),
            'total_reviews' => Review::count(),
        ];
    }

    private function getFinancialStats(): array
    {
        return [
            'total_transactions_value' => Bid::where('status', 'accepted')->sum('amount'),
            'average_bid_amount' => round(Bid::avg('amount') ?? 0, 2),
        ];
    }

    private function getActivityStats(): array
    {
        $totalBids = Bid::count();
        $acceptedBids = Bid::where('status', 'accepted')->count();
        $conversionRate = $totalBids > 0 ? ($acceptedBids / $totalBids) * 100 : 0;

        return [
            'new_projects_this_week' => Project::where('created_at', '>=', now()->subDays(7))->count(),
            'bids_conversion_rate' => round($conversionRate, 2).'%',
        ];
    }
}
