<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Web Development',
                'short_description' => 'Custom, high-performance websites and web applications.',
                'description' => 'We design and build fast, scalable websites and web applications tailored to your business goals, from marketing sites to complex platforms.',
                'icon' => 'icon-code',
                'sort_order' => 1,
            ],
            [
                'title' => 'Mobile App Development',
                'short_description' => 'Native and cross-platform mobile apps that users love.',
                'description' => 'Our team builds polished iOS and Android applications using modern cross-platform tooling, focused on performance and usability.',
                'icon' => 'icon-mobile',
                'sort_order' => 2,
            ],
            [
                'title' => 'UI/UX Design',
                'short_description' => 'User-centered interface and experience design.',
                'description' => 'We craft intuitive, beautiful interfaces backed by research and testing, helping your product feel effortless to use.',
                'icon' => 'icon-design',
                'sort_order' => 3,
            ],
            [
                'title' => 'SEO',
                'short_description' => 'Search engine optimization that drives organic growth.',
                'description' => 'From technical audits to content strategy, we help your brand rank higher and attract qualified organic traffic.',
                'icon' => 'icon-search',
                'sort_order' => 4,
            ],
            [
                'title' => 'Digital Marketing',
                'short_description' => 'Full-funnel campaigns across paid and social channels.',
                'description' => 'We plan and execute digital marketing campaigns across search, social, and email to grow your audience and revenue.',
                'icon' => 'icon-marketing',
                'sort_order' => 5,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['title' => $service['title']], $service);
        }
    }
}
