<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $portfolios = [
            [
                'title' => 'E-Commerce Platform',
                'client_name' => 'Demo Tech',
                'category' => 'Web Development',
                'short_description' => 'A high-conversion e-commerce storefront with a custom checkout flow.',
                'description' => 'We built a fully custom e-commerce platform with product catalog management, cart, and a streamlined checkout experience.',
                'technologies' => ['Laravel', 'React', 'Stripe'],
                'project_date' => now()->subMonths(4)->toDateString(),
                'is_featured' => true,
            ],
            [
                'title' => 'Corporate Website',
                'client_name' => 'Creative Labs',
                'category' => 'Web Development',
                'short_description' => 'A modern corporate site with a headless CMS-driven blog.',
                'description' => 'A complete redesign and rebuild of a corporate marketing website, including a blog powered by a headless CMS.',
                'technologies' => ['Laravel', 'Next.js', 'SEO'],
                'project_date' => now()->subMonths(2)->toDateString(),
                'is_featured' => true,
            ],
            [
                'title' => 'Marketing Dashboard',
                'client_name' => 'Demo Tech',
                'category' => 'Digital Strategy',
                'short_description' => 'An internal analytics dashboard for tracking campaign performance.',
                'description' => 'A custom analytics dashboard aggregating data from Google Ads, Meta Ads, and internal CRM data into one view.',
                'technologies' => ['Laravel', 'Meta Ads', 'Google Ads'],
                'project_date' => now()->subMonth()->toDateString(),
                'is_featured' => false,
            ],
        ];

        foreach ($portfolios as $portfolio) {
            Portfolio::updateOrCreate(['title' => $portfolio['title']], $portfolio);
        }
    }
}
