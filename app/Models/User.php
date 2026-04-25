<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AvailabilityStatus;
use App\Models\City;
use App\Models\Freelancer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'type',
        'country',
        'city_id'
    ];
    protected $hidden = ['password'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'city_id' => 'integer',
            'password' => 'hashed'
        ];
    }
    protected $appends = [
        'full_name',
        'member_since'
    ];
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function freelancerProfile()
    {
        return $this->hasOne(Freelancer::class);
    }

    public function profile()
    {
        return $this->freelancerProfile();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class)->withPivot('years_of_experience');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class, 'freelancer_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Accessors:
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function getMemberSinceAttribute()
    {
        return "Member since " . ($this->created_at?->format('F Y'));
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = preg_replace('/[^0-9+]/', '', $value);
    }

    //scopes:

    public function isActiveScope()
    {
        return $this->where('is_active', true);
    }
    public function scopeAvailable()
    {
        return $this->where('availability', AvailabilityStatus::AVAILABLE);
    }
    public function scopeHighestRated($query)
    {
        return $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
    }

    public function isFreelancer(): bool
    {
        return $this->type === 'freelancer';
    }

    public function isClient(): bool
    {
        return $this->type === 'client';
    }
}
