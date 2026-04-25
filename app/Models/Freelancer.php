<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;

    protected $table = 'freelancer_profiles';

    protected $fillable = [
        'user_id',
        'profile_picture',
        'bio',
        'hourly_rate',
        'availability',
        'portfolio_links',
        'skills_summary',
        'verified'
    ];

    protected $casts = [
        'portfolio_links' => 'array',
        'skills_summary' => 'array',
        'hourly_rate' => 'decimal:2',
        'verified' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'skill_user',
            'user_id',
            'skill_id'
        )->withPivot('years_of_experience');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->profile_picture
            ? url('storage/' . $this->profile_picture)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->user?->full_name ?? '');
    }

    public function reviewsAvgRating()
    {
        return $this->hasOneThrough(Review::class, User::class, 'id', 'reviewable_id')
            ->where('reviewable_type', Freelancer::class)
            ->selectRaw('reviewable_id, avg(rating) as average')
            ->groupBy('reviewable_id');
    }

        public function scopeVerified()
    {
        return $this->where('verified', true);
    }
}
