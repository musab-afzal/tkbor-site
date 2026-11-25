<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What services does your agency offer?',
                'answer' => 'We offer web development, mobile app development, UI/UX design, SEO, and digital marketing services tailored to your business.',
                'sort_order' => 1,
            ],
            [
                'question' => 'How long does a typical project take?',
                'answer' => 'Timelines vary by scope, but most website projects take 4-8 weeks from kickoff to launch, while larger platforms can take longer.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Do you offer ongoing support after launch?',
                'answer' => 'Yes, we offer maintenance and support retainers to keep your site secure, updated, and performing well after launch.',
                'sort_order' => 3,
            ],
            [
                'question' => 'How do you price your projects?',
                'answer' => 'Most projects are quoted as a fixed price based on scope, though we also offer hourly and monthly retainer arrangements.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Can you work with our existing brand guidelines?',
                'answer' => 'Absolutely. We can design within your existing brand guidelines or help you develop new ones as part of the engagement.',
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
