<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Creative Marketing Teams Driving Digital Success',
                'short_description' => 'How cross-functional creative teams are reshaping digital marketing outcomes.',
                'content' => "Digital marketing success increasingly depends on tight collaboration between design, content, and data teams. In this post we explore how creative marketing teams structure their workflows to move faster without sacrificing quality.\n\nWe look at real examples of campaign planning, creative production, and performance review cycles that keep teams aligned and effective.",
                'author_name' => 'Super Admin',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Strategic Business Planning For Agency Growth',
                'short_description' => 'A practical framework for planning sustainable agency growth.',
                'content' => "Growing a digital agency sustainably requires more than winning new clients — it requires deliberate planning around capacity, service lines, and pricing.\n\nThis article walks through a simple framework agencies can use to plan quarterly growth targets while protecting delivery quality.",
                'author_name' => 'Super Admin',
                'status' => 'published',
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Professional Collaboration In Creative Workspace',
                'short_description' => 'Why the physical and virtual workspace still shapes creative output.',
                'content' => "Whether remote, hybrid, or in-office, the way creative teams collaborate has a direct effect on the quality of their output.\n\nWe share a few lightweight practices — from async design reviews to weekly critique sessions — that help creative teams stay sharp.",
                'author_name' => 'Super Admin',
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($posts as $post) {
            Blog::updateOrCreate(['title' => $post['title']], $post);
        }
    }
}
