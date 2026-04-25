<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'budget_type',
        'budget',
        'deadline',
        'status',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'date',
    ];

    protected $appends = [
        'budget_format',
        'deadline_remaining',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    protected function budgetFormat(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->budget_type === 'fixed'
                ? "{$this->budget} USD"
                : "{$this->budget}$/hr"
        );
    }

    protected function getDeadlineRemainingAttribute()
    {
        return Attribute::make(
            get: function () {
                $deadline = Carbon::parse($this->deadline);
                if ($deadline->isPast()) {
                    return "Expired";
                }
                $days = (int) now()->diffInDays($deadline, false);
                return $days . " days left";
            }
        );
    }
    //scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeMinBudget($query, $amount)
    {
        return $query->where('budget', '>=', $amount);
    }

    public function scopePublishedThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
    }
}
