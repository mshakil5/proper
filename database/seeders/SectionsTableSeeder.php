<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Section::truncate();

        $sections = [
            ['name' => 'hero', 'sl' => 1, 'status' => 1],
            ['name' => 'menu', 'sl' => 2, 'status' => 1],
            ['name' => 'our-story', 'sl' => 3, 'status' => 1],
            ['name' => 'find-us', 'sl' => 4, 'status' => 1]
        ];

        foreach ($sections as $section) {
            Section::create($section);
        }
    }
}