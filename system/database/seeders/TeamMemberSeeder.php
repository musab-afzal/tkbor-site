<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Thomas Adison',
                'designation' => 'Creative Director',
                'bio' => 'Thomas leads our creative team, bringing over a decade of brand and digital design experience.',
                'email' => 'thomas@misiomagency.example.com',
                'sort_order' => 1,
            ],
            [
                'name' => 'Christine Rose',
                'designation' => 'Lead Developer',
                'bio' => 'Christine oversees our engineering team and specializes in scalable Laravel applications.',
                'email' => 'christine@misiomagency.example.com',
                'sort_order' => 2,
            ],
            [
                'name' => 'Kevin Martin',
                'designation' => 'Digital Strategist',
                'bio' => 'Kevin builds data-driven marketing strategies that help clients grow their online presence.',
                'email' => 'kevin@misiomagency.example.com',
                'sort_order' => 3,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(['name' => $member['name']], $member);
        }
    }
}
