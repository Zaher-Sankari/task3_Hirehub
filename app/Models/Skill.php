<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    
    #[Fillable(['name'])]
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('years_of_experience');
    }
}
