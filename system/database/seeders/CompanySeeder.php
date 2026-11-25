<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Demo Tech',
                'website_url' => 'https://demotech.example.com',
                'description' => 'A technology company we partnered with on product design and development.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Creative Labs',
                'website_url' => 'https://creativelabs.example.com',
                'description' => 'A creative studio we collaborated with on brand identity and campaigns.',
                'sort_order' => 2,
            ],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(['name' => $company['name']], $company);
        }
    }
}
