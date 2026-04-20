<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;
    
    protected $table = 'profile_freelancer';
    
    protected $fillable = [       
        'user_id', 'profile_picture', 'bio', 'phone', 'hourly_rate',
        'availability', 'portfolio_links', 'skills_summary'
    ];

    protected $casts = [
        'portfolio_links' => 'array',
        'skills_summary' => 'array',
        'hourly_rate' => 'decimal:2',
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

    public function getRatingDisplayAttribute(): string
    {
        $avg = $this->user?->reviews()->avg('rating'); 
        return $avg ? number_format($avg, 1) . ' ⭐' : 'No reviews yet';
    }
}