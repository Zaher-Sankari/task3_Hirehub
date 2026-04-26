<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProjectTagSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $tags = Tag::all();
        
        foreach ($projects as $project) {
            $randomTags = $tags->random(rand(2, 5));
            $project->tags()->attach($randomTags->pluck('id')->toArray());
        }
    }
}