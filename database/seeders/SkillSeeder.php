<?php
namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        Skill::insert([
            ['name' => 'PHP'],
            ['name' => 'JavaScript'],
            ['name' => 'Flutter'],
            ['name' => 'Figma'],
            ['name' => 'MySQL'],
        ]);
    }
}