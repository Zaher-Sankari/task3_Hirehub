<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
       use HasFactory;

    protected $fillable = [
        'project_id',
        'rating',
        'comment',
        'reviewable_type',
        'reviewable_id',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
