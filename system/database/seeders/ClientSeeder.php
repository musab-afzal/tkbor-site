<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'John Carter',
                'company_name' => 'Demo Tech',
                'email' => 'john.carter@demotech.example.com',
                'phone' => '+1 555 0101',
                'website' => 'https://demotech.example.com',
                'description' => 'Long-term retainer client for web development and SEO.',
            ],
            [
                'name' => 'Sara Malik',
                'company_name' => 'Creative Labs',
                'email' => 'sara.malik@creativelabs.example.com',
                'phone' => '+1 555 0102',
                'website' => 'https://creativelabs.example.com',
                'description' => 'Ongoing brand identity and digital marketing partner.',
            ],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(['email' => $client['email']], $client);
        }
    }
}
