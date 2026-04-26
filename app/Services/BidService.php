<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BidService
{
    /**
     * Place a new bid on a project
     * 
     * All validation is handled in StoreBidRequest
     * This method only handles database operations
     */
    public function placeBid(User $user, array $data, Project $project): Bid
    {
        return DB::transaction(function () use ($user, $data, $project) {
            $bid = Bid::create([
                'project_id' => $project->id,
                'freelancer_id' => $user->id,
                'amount' => $data['amount'],
                'delivery_days' => $data['delivery_days'],
                'proposal' => $data['proposal'],
                'status' => 'pending',
            ]);
            
            return $bid->load(['project' => function ($query) {
                $query->select('id', 'title', 'budget', 'budget_type', 'status', 'client_id');
            }]);
        });
    }

    /**
     * Accept a bid and update related data
     * 
     * All business rules are validated in AcceptBidRequest
     */
    public function acceptBid(Bid $bid): Bid
    {
        return DB::transaction(function () use ($bid) {
            // Accept this bid
            $bid->update(['status' => 'accepted']);
            
            // Update project status to in_progress
            $bid->project->update(['status' => 'in_progress']);
            
            // Reject all other pending bids for this project
            $bid->project->bids()
                ->where('id', '!=', $bid->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);
            
            return $bid->load([
                'project' => function ($query) {
                    $query->select('id', 'title', 'budget', 'budget_type', 'status', 'client_id');
                },
                'freelancer' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'email');
                },
                'freelancer.freelancerProfile'
            ]);
        });
    }
    
    /**
     * Reject a bid
     * 
     * All business rules are validated in RejectBidRequest
     */
    public function rejectBid(Bid $bid): Bid
    {
        $bid->update(['status' => 'rejected']);
        
        return $bid->load([
            'project' => function ($query) {
                $query->select('id', 'title', 'status');
            }
        ]);
    }

    /**
     * Get bid with status-appropriate relations (Phase 6 requirement)
     */
    public function getBidWithRelations(Bid $bid): Bid
    {
        // Always load basic relations
        $bid->load([
            'project' => function ($query) {
                $query->select('id', 'title', 'budget', 'budget_type', 'status', 'client_id');
            },
            'freelancer' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'email');
            }
        ]);
        
        // Load additional relations based on bid status
        if ($bid->status === 'accepted') {
            $bid->load([
                'project.client:id,first_name,last_name,email',
                'freelancer.freelancerProfile:id,user_id,bio,hourly_rate,availability',
                'attachments'
            ]);
        } elseif ($bid->status === 'pending') {
            $bid->load(['attachments']);
        } elseif ($bid->status === 'rejected') {
            // Rejected bids get minimal data (hide proposal for privacy)
            $bid->makeHidden(['proposal']);
        }
        
        return $bid;
    }

    /**
     * Get all bids for a project (with filters)
     */
    public function getProjectBids(Project $project, array $filters = [])
    {
        $query = $project->bids()->with([
            'freelancer:id,first_name,last_name,email',
            'freelancer.freelancerProfile:id,user_id,hourly_rate'
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['min_amount'])) {
            $query->where('amount', '>=', $filters['min_amount']);
        }

        if (!empty($filters['max_amount'])) {
            $query->where('amount', '<=', $filters['max_amount']);
        }

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all bids by a freelancer (with filters)
     */
    public function getFreelancerBids(User $freelancer, array $filters = [])
    {
        $query = $freelancer->bids()->with([
            'project:id,title,budget,budget_type,status,deadline'
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}